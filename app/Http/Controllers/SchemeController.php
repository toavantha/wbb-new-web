<?php

namespace App\Http\Controllers;

use App\Models\WelfareScheme;
use Illuminate\View\View;

class SchemeController extends Controller
{
    /**
     * Show overview of welfare benefit schemes including Aswesuma, Elderly, Disabled, CKDu.
     */
    public function index(): View
    {
        $schemes = WelfareScheme::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (WelfareScheme $welfareScheme): array => $welfareScheme->publicCard());

        return view('frontend.schemes', compact('schemes'));
    }
}
