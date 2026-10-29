<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProcurementController extends Controller
{
    /**
     * Show procurement notices, tender documents, TORs, and other notices.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'current');
        if (! in_array($status, ['current', 'expired'], true)) {
            $status = 'current';
        }

        $category = $request->query('category', 'All');
        $search = trim((string) $request->query('search', ''));
        $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
        $searchPattern = '%'.$escapedSearch.'%';
        $today = Carbon::today();

        $query = Procurement::query()->where('is_active', true);

        if ($status === 'expired') {
            $query->where('deadline_date', '<', $today)
                ->orderBy('deadline_date', 'desc');
        } else {
            $query->where('deadline_date', '>=', $today)
                ->orderBy('deadline_date', 'desc');
        }

        if ($category !== 'All' && in_array($category, Procurement::CATEGORIES, true)) {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($searchPattern) {
                foreach ([
                    'title', 'title_si', 'title_ta', 'reference_no',
                    'description', 'description_si', 'description_ta',
                ] as $index => $field) {
                    $condition = $q->getQuery()->getGrammar()->wrap($field)." LIKE ? ESCAPE '!'";

                    if ($index === 0) {
                        $q->whereRaw($condition, [$searchPattern]);
                    } else {
                        $q->orWhereRaw($condition, [$searchPattern]);
                    }
                }
            });
        }

        $procurements = $query->paginate(10)->withQueryString();

        $currentCount = Procurement::query()->where('is_active', true)->where('deadline_date', '>=', $today)->count();
        $expiredCount = Procurement::query()->where('is_active', true)->where('deadline_date', '<', $today)->count();

        $categories = [
            'All' => __('site.procurements.all_categories'),
            'Procurement Notice' => __('site.procurements.category_notice'),
            'Tender Document' => __('site.procurements.category_tender'),
            'TOR' => __('site.procurements.category_tor'),
            'Other' => __('site.procurements.category_other'),
        ];

        return view('frontend.procurements', compact(
            'procurements',
            'status',
            'category',
            'search',
            'currentCount',
            'expiredCount',
            'categories'
        ));
    }

    /**
     * Download the procurement document for requested or current locale, with fallback to English.
     */
    public function download(Procurement $procurement, ?string $locale = null): StreamedResponse|RedirectResponse
    {
        abort_unless($procurement->is_active || auth()->check(), 404);

        $locale ??= app()->getLocale();
        $filePath = $procurement->localizedFilePath($locale);

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            $filePath = $procurement->file_path;
        }

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'The requested procurement document is not available.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename = ($procurement->reference_no ? Str::slug($procurement->reference_no).'-' : '').Str::slug($procurement->localizedTitle($locale)).'.'.$extension;

        return Storage::disk('public')->download($filePath, $filename);
    }

    /**
     * View the procurement document online in browser.
     */
    public function view(Procurement $procurement, ?string $locale = null): StreamedResponse|RedirectResponse
    {
        abort_unless($procurement->is_active || auth()->check(), 404);

        $locale ??= app()->getLocale();
        $filePath = $procurement->localizedFilePath($locale);

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            $filePath = $procurement->file_path;
        }

        if (! $filePath || ! Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'The requested procurement document is not available.');
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = Storage::disk('public')->mimeType($filePath) ?: 'application/pdf';
        $filename = ($procurement->reference_no ? Str::slug($procurement->reference_no).'-' : '').Str::slug($procurement->localizedTitle($locale)).'.'.$extension;

        if (in_array($extension, ['docx', 'doc', 'zip'], true)) {
            return Storage::disk('public')->download($filePath, $filename);
        }

        return Storage::disk('public')->response(
            $filePath,
            $filename,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            ]
        );
    }
}
