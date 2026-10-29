<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\GovernmentAffiliation;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Show institutional details, legal mandate, vision, mission and board members.
     */
    public function index(): View
    {
        $boardMembers = BoardMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (BoardMember $member): array => [
                'name' => $member->localizedName(),
                'title' => __('site.about_people.'.$member->position.'_title'),
                'organization' => __('site.about_people.organization'),
                'email' => $member->email,
                'image' => $member->imageUrl(),
            ]);

        $ministryLeadership = GovernmentAffiliation::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (GovernmentAffiliation $affiliation): array => [
                'name' => $affiliation->localizedName(),
                'title' => $affiliation->localizedTitle(),
                'ministry' => $affiliation->localizedMinistry(),
                'image' => $affiliation->imageUrl(),
            ]);

        return view('frontend.about', compact('boardMembers', 'ministryLeadership'));
    }
}
