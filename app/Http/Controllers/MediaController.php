<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Show news, press releases, workshops and media gallery.
     */
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $query = NewsItem::where('is_published', true);

        if (!empty($category) && $category !== 'All') {
            $query->where('category', $category);
        }

        $news = $query->orderBy('published_date', 'desc')->paginate(6)->withQueryString();

        $featured = NewsItem::where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('published_date', 'desc')
            ->first();

        $categories = ['All', 'Announcement', 'Press Release', 'Notice', 'Workshop'];

        return view('frontend.media', compact('news', 'featured', 'categories', 'category'));
    }

    /**
     * Show single news article.
     */
    public function show(string $slug): View
    {
        $article = NewsItem::with('images')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $recentArticles = NewsItem::where('is_published', true)
            ->where('id', '!=', $article->id)
            ->orderBy('published_date', 'desc')
            ->take(4)
            ->get();

        return view('frontend.media_detail', compact('article', 'recentArticles'));
    }
}
