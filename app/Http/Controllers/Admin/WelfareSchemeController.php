<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelfareScheme;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WelfareSchemeController extends Controller
{
    public function index(): View
    {
        $welfareSchemes = WelfareScheme::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);

        return view('admin.welfare-schemes.index', compact('welfareSchemes'));
    }

    public function create(): View
    {
        $nextSortOrder = ((int) WelfareScheme::query()->max('sort_order')) + 1;

        return view('admin.welfare-schemes.create', compact('nextSortOrder'));
    }

    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $this->validatedInput($request);
        $validated['is_active'] = $request->boolean('is_active');
        $welfareScheme = WelfareScheme::create($validated);
        $activityLogger->record(
            $request,
            'created',
            'welfare_scheme',
            $welfareScheme->id,
            $welfareScheme->localizedContent()['name'],
        );

        return redirect()->route('admin.welfare-schemes.index')->with('success', 'Welfare scheme added successfully.');
    }

    public function edit(WelfareScheme $welfareScheme): View
    {
        return view('admin.welfare-schemes.edit', compact('welfareScheme'));
    }

    public function update(
        Request $request,
        WelfareScheme $welfareScheme,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $validated = $this->validatedInput($request);
        $validated['is_active'] = $request->boolean('is_active');
        $welfareScheme->update($validated);
        $activityLogger->record(
            $request,
            'updated',
            'welfare_scheme',
            $welfareScheme->id,
            $welfareScheme->localizedContent()['name'],
        );

        return redirect()->route('admin.welfare-schemes.index')->with('success', 'Welfare scheme updated successfully.');
    }

    public function destroy(
        WelfareScheme $welfareScheme,
        Request $request,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $schemeId = $welfareScheme->id;
        $schemeName = $welfareScheme->localizedContent()['name'];
        $welfareScheme->delete();
        $activityLogger->record($request, 'deleted', 'welfare_scheme', $schemeId, $schemeName);

        return redirect()->route('admin.welfare-schemes.index')->with('success', 'Welfare scheme deleted successfully.');
    }

    /**
     * @return array{content: array<string, array{name: string, badge: string, duration: string, description: string, indicators: array<int, string>}>, monthly_allowance: int, sort_order: int}
     */
    private function validatedInput(Request $request): array
    {
        $rules = [
            'monthly_allowance' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];

        foreach (['en', 'si', 'ta'] as $locale) {
            $rules["content.{$locale}.name"] = ['required', 'string', 'max:191'];
            $rules["content.{$locale}.badge"] = ['required', 'string', 'max:191'];
            $rules["content.{$locale}.duration"] = ['required', 'string', 'max:191'];
            $rules["content.{$locale}.description"] = ['required', 'string', 'max:2000'];
            $rules["content.{$locale}.indicators"] = ['required', 'string', 'max:4000'];
        }

        $validated = $request->validate($rules);

        foreach (['en', 'si', 'ta'] as $locale) {
            $indicators = preg_split('/\r\n|\r|\n/', $validated['content'][$locale]['indicators']);
            $indicators = array_values(array_filter(array_map('trim', $indicators), fn (string $indicator): bool => $indicator !== ''));

            if ($indicators === []) {
                throw ValidationException::withMessages([
                    "content.{$locale}.indicators" => 'Enter at least one criterion.',
                ]);
            }

            $validated['content'][$locale]['indicators'] = $indicators;
        }

        return $validated;
    }
}
