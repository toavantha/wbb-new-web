<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request, GlobalSearch $globalSearch): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:191'],
        ]);
        $query = trim($validated['q'] ?? '');
        $allResults = $query === '' ? [] : $globalSearch->search($query);
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $results = new LengthAwarePaginator(
            array_slice($allResults, ($currentPage - 1) * $perPage, $perPage),
            count($allResults),
            $perPage,
            $currentPage,
            [
                'path' => route('search'),
                'query' => ['q' => $query],
            ]
        );

        return view('frontend.search', compact('query', 'results'));
    }
}
