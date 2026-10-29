<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DownloadController extends Controller
{
    /**
     * Show official downloadable forms and guidelines.
     */
    public function index(Request $request): View
    {
        $language = $request->input('language');
        $query = Publication::where('is_active', true)
            ->whereIn('category', ['Application Form', 'Handbook', 'Guideline']);

        if (!empty($language) && $language !== 'All') {
            $query->where('language', $language);
        }

        $downloads = $query->orderBy('published_date', 'desc')->paginate(10)->withQueryString();

        $languages = ['All', 'Sinhala', 'Tamil', 'English', 'Trilingual'];

        return view('frontend.downloads', compact('downloads', 'languages', 'language'));
    }
}
