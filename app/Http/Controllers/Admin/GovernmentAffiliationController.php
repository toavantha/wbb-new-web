<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentAffiliation;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class GovernmentAffiliationController extends Controller
{
    public function index(CmsActivityLogger $activityLogger): View
    {
        $affiliations = GovernmentAffiliation::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10);
        $photoUploaders = $activityLogger->latestActorsFor('government_affiliation_photo', $affiliations->pluck('id')->all());

        return view('admin.government-affiliations.index', compact('affiliations', 'photoUploaders'));
    }

    public function create(): View
    {
        $nextSortOrder = ((int) GovernmentAffiliation::query()->max('sort_order')) + 1;

        return view('admin.government-affiliations.create', compact('nextSortOrder'));
    }

    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $this->validatedInput($request);
        unset($validated['image']);

        $uploadedImage = $request->file('image');

        if ($uploadedImage !== null) {
            $imagePath = $uploadedImage->store('government-affiliations', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the government affiliation photo.');
            }

            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $affiliation = GovernmentAffiliation::create($validated);
        $activityLogger->record($request, 'created', 'government_affiliation', $affiliation->id, $affiliation->name_en);

        if ($uploadedImage !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'government_affiliation_photo',
                $affiliation->id,
                $affiliation->name_en,
                'Uploaded government affiliation photo: '.$uploadedImage->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.government-affiliations.index')->with('success', 'Government affiliation added successfully.');
    }

    public function edit(GovernmentAffiliation $governmentAffiliation): View
    {
        return view('admin.government-affiliations.edit', compact('governmentAffiliation'));
    }

    public function update(
        Request $request,
        GovernmentAffiliation $governmentAffiliation,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $validated = $this->validatedInput($request);
        unset($validated['image']);

        $uploadedImage = $request->file('image');
        $previousImagePath = $governmentAffiliation->image_path;

        if ($uploadedImage !== null) {
            $imagePath = $uploadedImage->store('government-affiliations', 'public');

            if (! is_string($imagePath)) {
                throw new RuntimeException('Unable to store the government affiliation photo.');
            }

            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $governmentAffiliation->update($validated);
        $activityLogger->record(
            $request,
            'updated',
            'government_affiliation',
            $governmentAffiliation->id,
            $governmentAffiliation->name_en,
        );

        if ($uploadedImage !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'government_affiliation_photo',
                $governmentAffiliation->id,
                $governmentAffiliation->name_en,
                'Replaced government affiliation photo with: '.$uploadedImage->getClientOriginalName(),
            );

            $this->deleteStoredImage($previousImagePath);

            if ($previousImagePath !== null) {
                $activityLogger->record(
                    $request,
                    'removed',
                    'government_affiliation_photo',
                    $governmentAffiliation->id,
                    $governmentAffiliation->name_en,
                    'Replaced previous government affiliation photo: '.basename($previousImagePath),
                );
            }
        }

        return redirect()->route('admin.government-affiliations.index')
            ->with('success', 'Government affiliation updated successfully.');
    }

    public function destroy(
        GovernmentAffiliation $governmentAffiliation,
        Request $request,
        CmsActivityLogger $activityLogger,
    ): RedirectResponse {
        $affiliationId = $governmentAffiliation->id;
        $affiliationName = $governmentAffiliation->name_en;
        $imagePath = $governmentAffiliation->image_path;

        $this->deleteStoredImage($imagePath);
        $governmentAffiliation->delete();

        $activityLogger->record($request, 'deleted', 'government_affiliation', $affiliationId, $affiliationName);

        if ($imagePath !== null) {
            $activityLogger->record(
                $request,
                'removed',
                'government_affiliation_photo',
                $affiliationId,
                $affiliationName,
                'Removed government affiliation photo: '.basename($imagePath),
            );
        }

        return redirect()->route('admin.government-affiliations.index')
            ->with('success', 'Government affiliation deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedInput(Request $request): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:191'],
            'name_si' => ['required', 'string', 'max:191'],
            'name_ta' => ['required', 'string', 'max:191'],
            'title_en' => ['required', 'string', 'max:191'],
            'title_si' => ['required', 'string', 'max:191'],
            'title_ta' => ['required', 'string', 'max:191'],
            'ministry_en' => ['required', 'string', 'max:191'],
            'ministry_si' => ['required', 'string', 'max:191'],
            'ministry_ta' => ['required', 'string', 'max:191'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }

    private function deleteStoredImage(?string $imagePath): void
    {
        if ($imagePath === null || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return;
        }

        if (! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Unable to remove the government affiliation photo.');
        }
    }
}
