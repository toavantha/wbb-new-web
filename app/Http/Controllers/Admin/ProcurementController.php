<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Services\CmsActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProcurementController extends Controller
{
    /**
     * List all procurement notices, tender documents, TORs, and other notices.
     */
    public function index(Request $request, CmsActivityLogger $activityLogger): View
    {
        $status = $request->query('status', 'all');
        $category = $request->query('category', 'All');
        $search = trim((string) $request->query('search', ''));
        $today = Carbon::today();

        $query = Procurement::query();

        if ($status === 'current') {
            $query->where('deadline_date', '>=', $today);
        } elseif ($status === 'expired') {
            $query->where('deadline_date', '<', $today);
        }

        if ($category !== 'All' && in_array($category, Procurement::CATEGORIES, true)) {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
            $searchPattern = '%'.$escapedSearch.'%';

            $query->where(function ($q) use ($searchPattern) {
                foreach (['title', 'title_si', 'title_ta', 'reference_no'] as $index => $field) {
                    $condition = $q->getQuery()->getGrammar()->wrap($field)." LIKE ? ESCAPE '!'";

                    if ($index === 0) {
                        $q->whereRaw($condition, [$searchPattern]);
                    } else {
                        $q->orWhereRaw($condition, [$searchPattern]);
                    }
                }
            });
        }

        $procurements = $query->orderBy('deadline_date', 'desc')->paginate(10)->withQueryString();
        $uploaders = $activityLogger->latestActorsFor('procurement', $procurements->pluck('id')->all());

        $currentCount = Procurement::where('deadline_date', '>=', $today)->count();
        $expiredCount = Procurement::where('deadline_date', '<', $today)->count();
        $totalCount = Procurement::count();

        return view('admin.procurements.index', compact(
            'procurements',
            'uploaders',
            'status',
            'category',
            'search',
            'currentCount',
            'expiredCount',
            'totalCount'
        ));
    }

    /**
     * Show create procurement form.
     */
    public function create(): View
    {
        return view('admin.procurements.create');
    }

    /**
     * Store new procurement notice.
     */
    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'title_si' => ['nullable', 'string', 'max:191'],
            'title_ta' => ['nullable', 'string', 'max:191'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(Procurement::CATEGORIES)],
            'description' => ['nullable', 'string'],
            'description_si' => ['nullable', 'string'],
            'description_ta' => ['nullable', 'string'],
            'published_date' => ['required', 'date'],
            'deadline_date' => ['required', 'date'],
            'file_en' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
            'file_si' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
            'file_ta' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('file_en')) {
            $file = $request->file('file_en');
            $path = $file->store('procurements/en', 'public');
            $validated['file_path'] = $path;
            $validated['file_size'] = $this->formatFileSize($file->getSize());
        }

        if ($request->hasFile('file_si')) {
            $file = $request->file('file_si');
            $path = $file->store('procurements/si', 'public');
            $validated['file_path_si'] = $path;
            $validated['file_size_si'] = $this->formatFileSize($file->getSize());
        }

        if ($request->hasFile('file_ta')) {
            $file = $request->file('file_ta');
            $path = $file->store('procurements/ta', 'public');
            $validated['file_path_ta'] = $path;
            $validated['file_size_ta'] = $this->formatFileSize($file->getSize());
        }

        unset($validated['file_en'], $validated['file_si'], $validated['file_ta']);

        $procurement = Procurement::create($validated);

        $activityLogger->record(
            $request,
            'created',
            'procurement',
            $procurement->id,
            $procurement->title,
            'Created procurement in category: '.$procurement->category
        );

        if ($request->hasFile('file_en') || $request->hasFile('file_si') || $request->hasFile('file_ta')) {
            $uploadedLangs = [];
            if ($request->hasFile('file_en')) {
                $uploadedLangs[] = 'EN';
            }
            if ($request->hasFile('file_si')) {
                $uploadedLangs[] = 'SI';
            }
            if ($request->hasFile('file_ta')) {
                $uploadedLangs[] = 'TA';
            }

            $activityLogger->record(
                $request,
                'uploaded',
                'procurement',
                $procurement->id,
                $procurement->title,
                'Uploaded procurement documents for: '.implode(', ', $uploadedLangs)
            );
        }

        return redirect()->route('admin.procurements.index')->with('success', 'Procurement created successfully.');
    }

    /**
     * Show edit procurement form.
     */
    public function edit(int $id): View
    {
        $procurement = Procurement::findOrFail($id);

        return view('admin.procurements.edit', compact('procurement'));
    }

    /**
     * Update procurement notice.
     */
    public function update(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $procurement = Procurement::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'title_si' => ['nullable', 'string', 'max:191'],
            'title_ta' => ['nullable', 'string', 'max:191'],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(Procurement::CATEGORIES)],
            'description' => ['nullable', 'string'],
            'description_si' => ['nullable', 'string'],
            'description_ta' => ['nullable', 'string'],
            'published_date' => ['required', 'date'],
            'deadline_date' => ['required', 'date'],
            'file_en' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
            'file_si' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
            'file_ta' => ['nullable', 'file', 'mimes:pdf,docx,zip', 'extensions:pdf,docx,zip', 'max:20480'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Check if deadline was changed / extended
        $newDeadline = Carbon::parse($validated['deadline_date'])->startOfDay();
        $oldDeadline = $procurement->deadline_date?->startOfDay();

        if ($oldDeadline && ! $newDeadline->equalTo($oldDeadline)) {
            if ($procurement->original_deadline_date === null) {
                $validated['original_deadline_date'] = $procurement->deadline_date;
            }

            $activityLogger->record(
                $request,
                'updated',
                'procurement',
                $procurement->id,
                $procurement->title,
                "Updated deadline from {$oldDeadline->format('Y-m-d')} to {$newDeadline->format('Y-m-d')}"
            );
        }

        // Handle English file update
        if ($request->hasFile('file_en')) {
            if ($procurement->file_path && Storage::disk('public')->exists($procurement->file_path)) {
                Storage::disk('public')->delete($procurement->file_path);
            }
            $file = $request->file('file_en');
            $path = $file->store('procurements/en', 'public');
            $validated['file_path'] = $path;
            $validated['file_size'] = $this->formatFileSize($file->getSize());
        }

        // Handle Sinhala file update
        if ($request->hasFile('file_si')) {
            if ($procurement->file_path_si && Storage::disk('public')->exists($procurement->file_path_si)) {
                Storage::disk('public')->delete($procurement->file_path_si);
            }
            $file = $request->file('file_si');
            $path = $file->store('procurements/si', 'public');
            $validated['file_path_si'] = $path;
            $validated['file_size_si'] = $this->formatFileSize($file->getSize());
        }

        // Handle Tamil file update
        if ($request->hasFile('file_ta')) {
            if ($procurement->file_path_ta && Storage::disk('public')->exists($procurement->file_path_ta)) {
                Storage::disk('public')->delete($procurement->file_path_ta);
            }
            $file = $request->file('file_ta');
            $path = $file->store('procurements/ta', 'public');
            $validated['file_path_ta'] = $path;
            $validated['file_size_ta'] = $this->formatFileSize($file->getSize());
        }

        unset($validated['file_en'], $validated['file_si'], $validated['file_ta']);

        $procurement->update($validated);

        $activityLogger->record(
            $request,
            'updated',
            'procurement',
            $procurement->id,
            $procurement->title,
            'Updated procurement details'
        );

        return redirect()->route('admin.procurements.index')->with('success', 'Procurement updated successfully.');
    }

    /**
     * Extend procurement notice deadline quickly.
     */
    public function extendDeadline(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $procurement = Procurement::findOrFail($id);

        $request->validate([
            'new_deadline_date' => ['required', 'date'],
        ]);

        $oldDeadline = $procurement->deadline_date?->format('Y-m-d');
        $newDeadline = Carbon::parse($request->input('new_deadline_date'))->format('Y-m-d');

        if ($procurement->original_deadline_date === null) {
            $procurement->original_deadline_date = $procurement->deadline_date;
        }

        $procurement->deadline_date = $newDeadline;
        $procurement->save();

        $activityLogger->record(
            $request,
            'updated',
            'procurement',
            $procurement->id,
            $procurement->title,
            "Extended deadline from {$oldDeadline} to {$newDeadline}"
        );

        return redirect()->back()->with('success', "Deadline for '{$procurement->title}' extended to {$newDeadline} successfully.");
    }

    /**
     * Delete procurement notice.
     */
    public function destroy(int $id, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $procurement = Procurement::findOrFail($id);

        $files = array_filter([
            $procurement->file_path,
            $procurement->file_path_si,
            $procurement->file_path_ta,
        ]);

        foreach ($files as $filePath) {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }

        $title = $procurement->title;
        $procurement->delete();

        $activityLogger->record(
            $request,
            'removed',
            'procurement',
            $id,
            $title,
            'Deleted procurement notice'
        );

        return redirect()->route('admin.procurements.index')->with('success', 'Procurement deleted successfully.');
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        return number_format($bytes / 1024, 0).' KB';
    }
}
