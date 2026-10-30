<?php

namespace App\Http\Controllers;

use App\Models\ImportantNotice;
use App\Models\NewsItem;
use App\Models\Procurement;
use App\Models\Publication;
use App\Models\WelfareScheme;
use Carbon\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the main public homepage of Welfare Benefits Board.
     */
    public function index(): View
    {
        $importantInformation = ImportantNotice::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $featuredNews = NewsItem::where('is_published', true)
            ->orderBy('published_date', 'desc')
            ->take(3)
            ->get();

        $latestCirculars = Publication::where('is_active', true)
            ->whereIn('category', ['Circular', 'Gazette', 'Application Form'])
            ->orderBy('published_date', 'desc')
            ->take(4)
            ->get();

        $today = Carbon::today();
        $latestProcurements = Procurement::where('is_active', true)
            ->where('deadline_date', '>=', $today)
            ->orderBy('deadline_date', 'desc')
            ->take(4)
            ->get();

        if ($latestProcurements->isEmpty()) {
            $latestProcurements = Procurement::where('is_active', true)
                ->orderBy('deadline_date', 'desc')
                ->take(4)
                ->get();
        }

        $homeSchemes = WelfareScheme::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (WelfareScheme $welfareScheme): array => $welfareScheme->publicCard());

        $stats = [
            'screened_families' => '3,500,000+',
            'active_beneficiaries' => '2,300,000+',
            'divisions' => '331',
            'state_banks' => '100%',
        ];

        return view('frontend.home', compact(
            'importantInformation',
            'featuredNews',
            'latestCirculars',
            'latestProcurements',
            'homeSchemes',
            'stats',
        ));
    }
}
