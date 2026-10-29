<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\ContactMessage;
use App\Models\NewsItem;
use App\Models\Publication;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show administrative management dashboard.
     */
    public function index(): View
    {
        $counts = [
            'news' => NewsItem::count(),
            'publications' => Publication::whereIn('category', ['Act', 'Gazette', 'Circular'])->count(),
            'downloads' => Publication::whereIn('category', ['Application Form', 'Handbook', 'Guideline'])->count(),
            'careers' => Career::where('is_active', true)->count(),
            'messages' => ContactMessage::where('status', 'New')->count(),
            'total_messages' => ContactMessage::count(),
        ];

        $recentNews = NewsItem::orderBy('published_date', 'desc')->take(5)->get();
        $recentPublications = Publication::orderBy('published_date', 'desc')->take(5)->get();
        $recentMessages = ContactMessage::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('counts', 'recentNews', 'recentPublications', 'recentMessages'));
    }
}
