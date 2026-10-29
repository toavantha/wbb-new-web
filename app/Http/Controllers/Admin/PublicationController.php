<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicationController extends Controller
{
    /**
     * List all publications and circulars.
     */
    public function index(CmsActivityLogger $activityLogger): View
    {
        $publications = Publication::orderBy('published_date', 'desc')->paginate(10);
        $uploaders = $activityLogger->latestActorsFor('document', $publications->pluck('id')->all());

        return view('admin.publications.index', compact('publications', 'uploaders'));
    }

    /**
     * Show create publication form.
     */
    public function create(): View
    {
        return view('admin.publications.create');
    }

    /**
     * Store new publication.
     */
    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'],
            'published_date' => ['required', 'date'],
        ]);

        $file = $request->file('file');
        $path = $file->store('publications', 'public');

        unset($validated['file']);
        $validated['file_path'] = $path;
        $validated['file_size'] = $this->formatFileSize($file->getSize());
        $validated['file_url'] = Storage::disk('public')->url($path);
        $validated['is_active'] = $request->boolean('is_active');

        $publication = Publication::create($validated);
        $activityLogger->record($request, 'created', 'document', $publication->id, $publication->title);
        $activityLogger->record(
            $request,
            'uploaded',
            'document',
            $publication->id,
            $publication->title,
            'Uploaded file: '.$file->getClientOriginalName(),
        );

        return redirect()->route('admin.publications.index')->with('success', 'Publication added successfully.');
    }

    /**
     * Show edit publication form.
     */
    public function edit(int $id): View
    {
        $publication = Publication::findOrFail($id);

        return view('admin.publications.edit', compact('publication'));
    }

    /**
     * Update publication.
     */
    public function update(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $publication = Publication::findOrFail($id);

        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'published_date' => ['required', 'date'],
        ];

        if (! $publication->file_path && ! $publication->getRawOriginal('file_url')) {
            $rules['file'] = ['required', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'];
        } else {
            $rules['file'] = ['nullable', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'];
        }

        $validated = $request->validate($rules);

        $uploadedFile = $request->file('file');

        if ($uploadedFile !== null) {
            $path = $uploadedFile->store('publications', 'public');

            if ($publication->file_path && Storage::disk('public')->exists($publication->file_path)) {
                Storage::disk('public')->delete($publication->file_path);
            }

            $validated['file_path'] = $path;
            $validated['file_size'] = $this->formatFileSize($uploadedFile->getSize());
            $validated['file_url'] = Storage::disk('public')->url($path);
        }

        unset($validated['file']);
        $validated['is_active'] = $request->boolean('is_active');

        $publication->update($validated);
        $activityLogger->record($request, 'updated', 'document', $publication->id, $publication->title);

        if ($uploadedFile !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'document',
                $publication->id,
                $publication->title,
                'Replaced file with: '.$uploadedFile->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.publications.index')->with('success', 'Publication updated successfully.');
    }

    /**
     * Delete publication.
     */
    public function destroy(int $id, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $publication = Publication::findOrFail($id);

        $fileName = $publication->file_path ? basename($publication->file_path) : null;

        if ($publication->file_path && Storage::disk('public')->exists($publication->file_path)) {
            Storage::disk('public')->delete($publication->file_path);
        }

        $publication->delete();
        $activityLogger->record(
            $request,
            'deleted',
            'document',
            $publication->id,
            $publication->title,
            $fileName ? 'Deleted file: '.$fileName : null,
        );

        return redirect()->route('admin.publications.index')->with('success', 'Publication deleted successfully.');
    }

    /**
     * Format a file size in bytes to a human-readable string.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
