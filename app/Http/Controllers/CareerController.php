<?php

namespace App\Http\Controllers;

use App\Models\Career;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CareerController extends Controller
{
    /**
     * Show career vacancies and opportunities at Welfare Benefits Board.
     */
    public function index(): View
    {
        $careers = Career::where('is_active', true)
            ->orderBy('closing_date', 'asc')
            ->get();

        return view('frontend.careers', compact('careers'));
    }

    public function downloadDocument(Career $career, string $documentType): StreamedResponse|RedirectResponse
    {
        abort_unless($career->is_active, 404);
        abort_unless(in_array($documentType, Career::DOCUMENT_TYPES, true), 404);

        $locale = app()->getLocale();
        $field = $locale === 'en'
            ? "{$documentType}_file_path"
            : "{$documentType}_{$locale}_file_path";
        $path = $career->{$field};

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return back()->with('error', 'There are no documents uploaded');
        }

        return Storage::disk('public')->download($path);
    }
}
