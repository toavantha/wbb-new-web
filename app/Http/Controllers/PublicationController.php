<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicationController extends Controller
{
    private const LEGACY_PUBLICATION_HOSTS = [
        'wbb.gov.lk',
        'www.wbb.gov.lk',
    ];

    /**
     * Show official publications, acts, circulars, gazettes, and handbooks.
     */
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $search = $request->input('search');

        $query = Publication::where('is_active', true);

        if (! empty($category) && $category !== 'All') {
            $query->where('category', $category);
        }

        if (! empty($search)) {
            $escapedSearch = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search);
            $searchPattern = '%'.$escapedSearch.'%';

            $query->where(function ($q) use ($searchPattern) {
                foreach (['title', 'description', 'document_number'] as $index => $field) {
                    $condition = $q->getQuery()->getGrammar()->wrap($field)." LIKE ? ESCAPE '!'";

                    if ($index === 0) {
                        $q->whereRaw($condition, [$searchPattern]);
                    } else {
                        $q->orWhereRaw($condition, [$searchPattern]);
                    }
                }
            });
        }

        $publications = $query->orderBy('published_date', 'desc')->paginate(10)->withQueryString();

        $categories = [
            'All' => __('site.publications.categories.all'),
            'Circular' => __('site.publications.categories.circular'),
            'Act' => __('site.publications.categories.act'),
            'Gazette' => __('site.publications.categories.gazette'),
            'Handbook' => __('site.publications.categories.handbook'),
            'Application Form' => __('site.publications.categories.application_form'),
        ];

        return view('frontend.publications', compact('publications', 'categories', 'category', 'search'));
    }

    /**
     * Download or view the publication document file.
     */
    public function download(Publication $publication, Request $request): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        abort_unless($publication->is_active || auth()->check(), 404);

        if ($publication->file_path && Storage::disk('public')->exists($publication->file_path)) {
            $extension = pathinfo($publication->file_path, PATHINFO_EXTENSION);
            $mimeType = Storage::disk('public')->mimeType($publication->file_path) ?: 'application/octet-stream';
            $filename = ($publication->document_number ? Str::slug($publication->document_number).'-' : '').Str::slug($publication->title).'.'.$extension;

            $isWordDocument = in_array(strtolower($extension), ['doc', 'docx'], true);
            $isInlineView = $request->boolean('view') || (! $isWordDocument && ! $request->boolean('download'));

            if ($request->boolean('download') || (! $isInlineView)) {
                return Storage::disk('public')->download($publication->file_path, $filename);
            }

            return Storage::disk('public')->response(
                $publication->file_path,
                $filename,
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
                ]
            );
        }

        $rawUrl = $publication->getRawOriginal('file_url');
        if (! empty($rawUrl)) {
            $url = parse_url($rawUrl);
            abort_unless(is_array($url), 404, 'File not found');

            $scheme = strtolower($url['scheme'] ?? '');
            $host = strtolower($url['host'] ?? '');
            $port = $url['port'] ?? null;

            abort_unless(
                in_array($scheme, ['http', 'https'], true)
                    && in_array($host, self::LEGACY_PUBLICATION_HOSTS, true)
                    && ! isset($url['user'])
                    && ! isset($url['pass'])
                    && ($port === null || $port === 80 || $port === 443),
                404,
                'File not found'
            );

            $secureUrl = 'https://'.$host.($url['path'] ?? '/');

            if (isset($url['query'])) {
                $secureUrl .= '?'.$url['query'];
            }

            if (isset($url['fragment'])) {
                $secureUrl .= '#'.$url['fragment'];
            }

            return redirect()->away($secureUrl);
        }

        abort(404, 'File not found');
    }
}
