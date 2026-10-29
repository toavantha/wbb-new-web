<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CmsRegistrationAwaitingApproval;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class EmailVerificationController extends Controller
{
    public function verify(int $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            $administrators = User::query()
                ->where('role', 'admin')
                ->where('is_approved', true)
                ->get();

            Notification::send($administrators, new CmsRegistrationAwaitingApproval($user));
        }

        return redirect()->route('admin.login')->with(
            'success',
            'Your email has been verified. An administrator has been notified and must approve your account before you can sign in.'
        );
    }

    public function resend(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if ($user !== null && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()->route('admin.login')->with(
            'success',
            'If an unverified registration exists for that address, a verification email has been sent.'
        );
    }
}
