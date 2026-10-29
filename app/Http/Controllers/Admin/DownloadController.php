<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DownloadController extends Controller
{
    /**
     * List all downloadable forms and guidelines.
     */
    public function index(CmsActivityLogger $activityLogger): View
    {
        $downloads = Publication::whereIn('category', ['Application Form', 'Handbook', 'Guideline'])
            ->orderBy('published_date', 'desc')
            ->paginate(10);
        $uploaders = $activityLogger->latestActorsFor('document', $downloads->pluck('id')->all());

        return view('admin.downloads.index', compact('downloads', 'uploaders'));
    }

    /**
     * Show create download form.
     */
    public function create(): View
    {
        return view('admin.downloads.create');
    }

    /**
     * Store new download item.
     */
    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'in:Application Form,Handbook,Guideline'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'],
            'published_date' => ['required', 'date'],
        ]);

        $file = $request->file('file');
        $path = $file->store('downloads', 'public');

        unset($validated['file']);
        $validated['file_path'] = $path;
        $validated['file_size'] = $this->formatFileSize($file->getSize());
        $validated['file_url'] = Storage::disk('public')->url($path);
        $validated['is_active'] = $request->boolean('is_active');

        $download = Publication::create($validated);
        $activityLogger->record($request, 'created', 'document', $download->id, $download->title);
        $activityLogger->record(
            $request,
            'uploaded',
            'document',
            $download->id,
            $download->title,
            'Uploaded file: '.$file->getClientOriginalName(),
        );

        return redirect()->route('admin.downloads.index')->with('success', 'Downloadable item created successfully.');
    }

    /**
     * Show edit download form.
     */
    public function edit(int $id): View
    {
        $download = Publication::findOrFail($id);

        return view('admin.downloads.edit', compact('download'));
    }

    /**
     * Update download item.
     */
    public function update(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $download = Publication::findOrFail($id);

        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'in:Application Form,Handbook,Guideline'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'published_date' => ['required', 'date'],
        ];

        if (! $download->file_path && ! $download->getRawOriginal('file_url')) {
            $rules['file'] = ['required', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'];
        } else {
            $rules['file'] = ['nullable', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:20480'];
        }

        $validated = $request->validate($rules);

        $uploadedFile = $request->file('file');

        if ($uploadedFile !== null) {
            $path = $uploadedFile->store('downloads', 'public');

            if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
                Storage::disk('public')->delete($download->file_path);
            }

            $validated['file_path'] = $path;
            $validated['file_size'] = $this->formatFileSize($uploadedFile->getSize());
            $validated['file_url'] = Storage::disk('public')->url($path);
        }

        unset($validated['file']);
        $validated['is_active'] = $request->boolean('is_active');

        $download->update($validated);
        $activityLogger->record($request, 'updated', 'document', $download->id, $download->title);

        if ($uploadedFile !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'document',
                $download->id,
                $download->title,
                'Replaced file with: '.$uploadedFile->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.downloads.index')->with('success', 'Download item updated successfully.');
    }

    /**
     * Delete download item.
     */
    public function destroy(int $id, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $download = Publication::findOrFail($id);

        $fileName = $download->file_path ? basename($download->file_path) : null;

        if ($download->file_path && Storage::disk('public')->exists($download->file_path)) {
            Storage::disk('public')->delete($download->file_path);
        }

        $download->delete();
        $activityLogger->record(
            $request,
            'deleted',
            'document',
            $download->id,
            $download->title,
            $fileName ? 'Deleted file: '.$fileName : null,
        );

        return redirect()->route('admin.downloads.index')->with('success', 'Download item deleted successfully.');
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
