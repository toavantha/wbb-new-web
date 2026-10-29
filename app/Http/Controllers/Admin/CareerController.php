<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use LogicException;
use RuntimeException;

class CareerController extends Controller
{
    /**
     * List all job vacancies.
     */
    public function index(CmsActivityLogger $activityLogger): View
    {
        $careers = Career::orderBy('closing_date', 'desc')->paginate(10);
        $uploaders = $activityLogger->latestActorsFor('career_document', $careers->pluck('id')->all());

        return view('admin.careers.index', compact('careers', 'uploaders'));
    }

    /**
     * Show create vacancy form.
     */
    public function create(): View
    {
        return view('admin.careers.create');
    }

    /**
     * Store new vacancy.
     */
    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            ...$this->careerRules(),
        ]);

        $documentPaths = $this->storeDocuments($request);

        foreach ($this->documentInputs() as $input) {
            unset($validated[$input]);
        }

        $validated = [...$validated, ...$documentPaths];
        $validated['is_active'] = $request->boolean('is_active');

        $career = Career::create($validated);

        foreach (array_keys($documentPaths) as $field) {
            $activityLogger->record(
                $request,
                'uploaded',
                'career_document',
                $career->id,
                $career->title,
                'Uploaded '.$this->documentLabel($field).': '.$request->file($this->documentInputForPath($field))->getClientOriginalName(),
            );
        }

        return redirect()->route('admin.careers.index')->with('success', 'Job vacancy created successfully.');
    }

    /**
     * Show edit vacancy form.
     */
    public function edit(int $id): View
    {
        $career = Career::findOrFail($id);

        return view('admin.careers.edit', compact('career'));
    }

    /**
     * Update vacancy.
     */
    public function update(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $career = Career::findOrFail($id);

        $validated = $request->validate($this->careerRules());
        $newDocumentPaths = $this->storeDocuments($request);
        $previousDocumentPaths = [];

        foreach ($this->documentInputs() as $input) {
            unset($validated[$input]);
        }

        foreach ($newDocumentPaths as $field => $path) {
            $previousDocumentPaths[$field] = $career->{$field};
            $validated[$field] = $path;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $career->update($validated);

        $this->deleteDocuments(array_filter($previousDocumentPaths));

        foreach ($newDocumentPaths as $field => $path) {
            $activityLogger->record(
                $request,
                'uploaded',
                'career_document',
                $career->id,
                $career->title,
                'Replaced '.$this->documentLabel($field).' with: '.$request->file($this->documentInputForPath($field))->getClientOriginalName(),
            );
        }

        foreach (array_filter($previousDocumentPaths) as $field => $path) {
            $activityLogger->record(
                $request,
                'removed',
                'career_document',
                $career->id,
                $career->title,
                'Replaced previous '.$this->documentLabel($field).': '.basename($path),
            );
        }

        return redirect()->route('admin.careers.index')->with('success', 'Job vacancy updated successfully.');
    }

    /**
     * Delete vacancy.
     */
    public function destroy(int $id, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $career = Career::findOrFail($id);
        $documentPaths = [];

        foreach (Career::DOCUMENT_TYPES as $documentType) {
            foreach (Career::DOCUMENT_LOCALES as $locale) {
                $field = $this->documentPathField($documentType, $locale);

                if ($career->{$field}) {
                    $documentPaths[$field] = $career->{$field};
                }
            }
        }

        $career->delete();

        $this->deleteDocuments(array_values($documentPaths));

        foreach ($documentPaths as $field => $path) {
            $activityLogger->record(
                $request,
                'removed',
                'career_document',
                $career->id,
                $career->title,
                'Removed '.$this->documentLabel($field).': '.basename($path),
            );
        }

        return redirect()->route('admin.careers.index')->with('success', 'Job vacancy deleted successfully.');
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function careerRules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'department' => ['required', 'string', 'max:100'],
            'employment_type' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:100'],
            'salary_scale' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'qualifications' => ['required', 'string'],
            'closing_date' => ['required', 'date'],
        ];

        foreach ($this->documentInputs() as $input) {
            $rules[$input] = ['nullable', 'file', 'mimes:pdf,docx', 'extensions:pdf,docx', 'max:10240'];
        }

        return $rules;
    }

    /**
     * @return array<int, string>
     */
    private function documentInputs(): array
    {
        $inputs = [];

        foreach (Career::DOCUMENT_TYPES as $documentType) {
            foreach (Career::DOCUMENT_LOCALES as $locale) {
                $inputs[] = "{$documentType}_{$locale}_file";
            }
        }

        return $inputs;
    }

    private function documentPathField(string $documentType, string $locale): string
    {
        return $locale === 'en'
            ? "{$documentType}_file_path"
            : "{$documentType}_{$locale}_file_path";
    }

    private function documentInputForPath(string $pathField): string
    {
        foreach (Career::DOCUMENT_TYPES as $documentType) {
            foreach (Career::DOCUMENT_LOCALES as $locale) {
                if ($this->documentPathField($documentType, $locale) === $pathField) {
                    return "{$documentType}_{$locale}_file";
                }
            }
        }

        throw new LogicException('Unknown vacancy document path field.');
    }

    private function documentLabel(string $pathField): string
    {
        return str_replace('_', ' ', str_replace('_file_path', '', $pathField));
    }

    /**
     * @return array<string, string>
     */
    private function storeDocuments(Request $request): array
    {
        $storedPaths = [];

        foreach (Career::DOCUMENT_TYPES as $documentType) {
            foreach (Career::DOCUMENT_LOCALES as $locale) {
                $input = "{$documentType}_{$locale}_file";

                if (! $request->hasFile($input)) {
                    continue;
                }

                $path = $request->file($input)->store("vacancies/{$locale}/{$documentType}", 'public');

                if (! is_string($path)) {
                    $this->deleteDocuments(array_values($storedPaths));

                    throw new RuntimeException('Unable to store vacancy documents.');
                }

                $field = $this->documentPathField($documentType, $locale);
                $storedPaths[$field] = $path;
            }
        }

        return $storedPaths;
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteDocuments(array $paths): void
    {
        foreach ($paths as $path) {
            if (! Storage::disk('public')->delete($path)) {
                throw new RuntimeException('Unable to remove a vacancy document.');
            }
        }
    }
}
