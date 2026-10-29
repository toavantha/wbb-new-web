<?php

namespace App\Http\Controllers;

use App\Models\ContactInformation;
use App\Models\ContactMessage;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Show contact information, headquarters details, and feedback form.
     */
    public function index(): View
    {
        $contactInformation = ContactInformation::query()->findOrFail(1);
        $turnstileSiteKey = config('services.turnstile.site_key');
        $turnstileEnabled = is_string($turnstileSiteKey)
            && $turnstileSiteKey !== ''
            && is_string(config('services.turnstile.secret_key'))
            && config('services.turnstile.secret_key') !== '';

        return view('frontend.contact', compact('contactInformation', 'turnstileEnabled', 'turnstileSiteKey'));
    }

    /**
     * Handle public citizen contact form submission.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => ['nullable', 'string', 'regex:/\A0[0-9]{9}\z/'],
            'subject' => 'required|string|max:191',
            'message' => 'required|string|max:3000',
            'cf-turnstile-response' => [
                'bail',
                'required',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail) use ($request): void {
                    $siteKey = config('services.turnstile.site_key');
                    $secretKey = config('services.turnstile.secret_key');

                    if (! is_string($siteKey) || $siteKey === '' || ! is_string($secretKey) || $secretKey === '') {
                        $fail(__('site.contact.captcha_unavailable'));

                        return;
                    }

                    try {
                        $response = Http::asForm()
                            ->timeout(5)
                            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                                'secret' => $secretKey,
                                'response' => $value,
                                'remoteip' => $request->ip(),
                            ]);
                    } catch (ConnectionException $exception) {
                        Log::warning('Cloudflare Turnstile verification could not connect.', [
                            'exception' => $exception->getMessage(),
                        ]);
                        $fail(__('site.contact.captcha_unavailable'));

                        return;
                    }

                    if ($response->serverError()) {
                        Log::warning('Cloudflare Turnstile verification returned a server error.', [
                            'status' => $response->status(),
                        ]);
                        $fail(__('site.contact.captcha_unavailable'));

                        return;
                    }

                    if (! $response->successful() || $response->json('success') !== true) {
                        $fail(__('site.contact.captcha_invalid'));
                    }
                },
            ],
        ], [
            'phone.regex' => __('site.contact.phone_format_error'),
            'cf-turnstile-response.required' => __('site.contact.captcha_required'),
        ]);

        unset($validated['cf-turnstile-response']);

        ContactMessage::create($validated);

        return redirect()->route('contact.index')
            ->with('success', __('site.contact.success'));
    }
}
