<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Services\CmsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class NewsController extends Controller
{
    /**
     * List all news articles.
     */
    public function index(CmsActivityLogger $activityLogger): View
    {
        $news = NewsItem::orderBy('published_date', 'desc')->paginate(10);
        $contentUploaders = $activityLogger->latestActorsFor(
            'news',
            $news->pluck('id')->all(),
            ['created', 'updated'],
        );
        $photoUploaders = $activityLogger->latestActorsFor('news_photo', $news->pluck('id')->all());

        return view('admin.news.index', compact('news', 'contentUploaders', 'photoUploaders'));
    }

    /**
     * Show create news form.
     */
    public function create(): View
    {
        return view('admin.news.create');
    }

    /**
     * Store news article.
     */
    public function store(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'title_si' => ['nullable', 'string', 'max:191'],
            'title_ta' => ['nullable', 'string', 'max:191'],
            'category' => 'required|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'excerpt_si' => ['nullable', 'string', 'max:500'],
            'excerpt_ta' => ['nullable', 'string', 'max:500'],
            'content' => 'required|string',
            'content_si' => ['nullable', 'string'],
            'content_ta' => ['nullable', 'string'],
            'published_date' => 'required|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'main_image_si' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'main_image_ta' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images' => ['nullable', 'array', 'max:20'],
            'sub_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images_si' => ['nullable', 'array', 'max:20'],
            'sub_images_si.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images_ta' => ['nullable', 'array', 'max:20'],
            'sub_images_ta.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.mt_rand(100, 999);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        unset($validated['main_image'], $validated['main_image_si'], $validated['main_image_ta']);
        unset($validated['sub_images'], $validated['sub_images_si'], $validated['sub_images_ta']);

        $newsItem = NewsItem::create($validated);
        $activityLogger->record($request, 'created', 'news', $newsItem->id, $newsItem->title);
        $this->storeImages($request, $newsItem, $activityLogger);

        return redirect()->route('admin.news.index')->with('success', 'News announcement created successfully.');
    }

    /**
     * Show edit news form.
     */
    public function edit(int $id): View
    {
        $newsItem = NewsItem::with('images')->findOrFail($id);

        return view('admin.news.edit', compact('newsItem'));
    }

    /**
     * Update news article.
     */
    public function update(Request $request, int $id, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $newsItem = NewsItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'title_si' => ['nullable', 'string', 'max:191'],
            'title_ta' => ['nullable', 'string', 'max:191'],
            'category' => 'required|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'excerpt_si' => ['nullable', 'string', 'max:500'],
            'excerpt_ta' => ['nullable', 'string', 'max:500'],
            'content' => 'required|string',
            'content_si' => ['nullable', 'string'],
            'content_ta' => ['nullable', 'string'],
            'published_date' => 'required|date',
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'main_image_si' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'main_image_ta' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images' => ['nullable', 'array', 'max:20'],
            'sub_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images_si' => ['nullable', 'array', 'max:20'],
            'sub_images_si.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sub_images_ta' => ['nullable', 'array', 'max:20'],
            'sub_images_ta.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');

        unset(
            $validated['main_image'],
            $validated['main_image_si'],
            $validated['main_image_ta'],
            $validated['sub_images'],
            $validated['sub_images_si'],
            $validated['sub_images_ta'],
            $validated['remove_images'],
        );

        $previousMainImagePaths = [];
        foreach (['en', 'si', 'ta'] as $locale) {
            $mainImageField = $this->mainImageField($locale);
            $mainImagePathAttribute = $this->mainImagePathAttribute($locale);
            $previousMainImagePaths[$locale] = $newsItem->{$mainImagePathAttribute};

            if ($request->hasFile($mainImageField)) {
                $validated[$mainImagePathAttribute] = $this->storeImage(
                    $request->file($mainImageField),
                    'news/'.$newsItem->id.'/main/'.$locale,
                );
            }
        }

        $removedImages = $newsItem->images()
            ->whereIn('id', $request->input('remove_images', []))
            ->get();

        $newsItem->update($validated);
        $activityLogger->record($request, 'updated', 'news', $newsItem->id, $newsItem->title);
        foreach (['en', 'si', 'ta'] as $locale) {
            $this->recordUploadedMainImage($request, $newsItem, $activityLogger, $locale);
        }
        $this->storeSubImages($request, $newsItem, $activityLogger);

        foreach ($removedImages as $image) {
            $image->delete();

            if (! Storage::disk('public')->delete($image->image_path)) {
                throw new RuntimeException('Unable to remove a news gallery image.');
            }

            $activityLogger->record(
                $request,
                'removed',
                'news_photo',
                $newsItem->id,
                $newsItem->title,
                'Removed gallery photo: '.basename($image->image_path),
            );
        }

        foreach (['en', 'si', 'ta'] as $locale) {
            $mainImageField = $this->mainImageField($locale);
            $previousMainImagePath = $previousMainImagePaths[$locale];

            if (! $request->hasFile($mainImageField) || $previousMainImagePath === null) {
                continue;
            }

            if (! Storage::disk('public')->delete($previousMainImagePath)) {
                throw new RuntimeException('Unable to remove the previous news main image.');
            }

            $activityLogger->record(
                $request,
                'removed',
                'news_photo',
                $newsItem->id,
                $newsItem->title,
                'Replaced '.($locale === 'en' ? '' : strtoupper($locale).' ').'main photo: '.basename($previousMainImagePath),
            );
        }

        return redirect()->route('admin.news.index')->with('success', 'News announcement updated successfully.');
    }

    /**
     * Delete news article.
     */
    public function destroy(int $id, Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $newsItem = NewsItem::with('images')->findOrFail($id);
        $imagePaths = $newsItem->images->pluck('image_path')->all();

        if ($newsItem->main_image_path) {
            $imagePaths[] = $newsItem->main_image_path;
        }
        if ($newsItem->main_image_path_si) {
            $imagePaths[] = $newsItem->main_image_path_si;
        }
        if ($newsItem->main_image_path_ta) {
            $imagePaths[] = $newsItem->main_image_path_ta;
        }

        $newsItem->delete();

        foreach ($imagePaths as $imagePath) {
            if (! Storage::disk('public')->delete($imagePath)) {
                throw new RuntimeException('Unable to remove a news image.');
            }

            $activityLogger->record(
                $request,
                'removed',
                'news_photo',
                $newsItem->id,
                $newsItem->title,
                'Removed photo: '.basename($imagePath),
            );
        }

        $activityLogger->record($request, 'deleted', 'news', $newsItem->id, $newsItem->title);

        return redirect()->route('admin.news.index')->with('success', 'News article deleted successfully.');
    }

    private function storeImages(Request $request, NewsItem $newsItem, CmsActivityLogger $activityLogger): void
    {
        $mainImagePaths = [];
        foreach (['en', 'si', 'ta'] as $locale) {
            $mainImageField = $this->mainImageField($locale);

            if ($request->hasFile($mainImageField)) {
                $mainImagePaths[$this->mainImagePathAttribute($locale)] = $this->storeImage(
                    $request->file($mainImageField),
                    'news/'.$newsItem->id.'/main/'.$locale,
                );
                $this->recordUploadedMainImage($request, $newsItem, $activityLogger, $locale);
            }
        }

        if ($mainImagePaths !== []) {
            $newsItem->update($mainImagePaths);
        }

        $this->storeSubImages($request, $newsItem, $activityLogger);
    }

    private function recordUploadedMainImage(
        Request $request,
        NewsItem $newsItem,
        CmsActivityLogger $activityLogger,
        string $locale,
    ): void
    {
        $image = $request->file($this->mainImageField($locale));

        if ($image !== null) {
            $activityLogger->record(
                $request,
                'uploaded',
                'news_photo',
                $newsItem->id,
                $newsItem->title,
                'Uploaded '.($locale === 'en' ? '' : strtoupper($locale).' ').'main photo: '.$image->getClientOriginalName(),
            );
        }
    }

    private function storeSubImages(Request $request, NewsItem $newsItem, CmsActivityLogger $activityLogger): void
    {
        foreach (['en', 'si', 'ta'] as $locale) {
            $subImagesField = $this->subImagesField($locale);
            $sortOrder = $newsItem->images()->where('locale', $locale)->count();

            foreach ($request->file($subImagesField, []) as $image) {
                $path = $this->storeImage($image, 'news/'.$newsItem->id.'/gallery/'.$locale);

                $newsItem->images()->create([
                    'image_path' => $path,
                    'locale' => $locale,
                    'sort_order' => $sortOrder++,
                ]);

                $activityLogger->record(
                    $request,
                    'uploaded',
                    'news_photo',
                    $newsItem->id,
                    $newsItem->title,
                    'Uploaded '.($locale === 'en' ? '' : strtoupper($locale).' ').'gallery photo: '.$image->getClientOriginalName(),
                );
            }
        }
    }

    private function mainImageField(string $locale): string
    {
        return 'main_image'.($locale === 'en' ? '' : '_'.$locale);
    }

    private function mainImagePathAttribute(string $locale): string
    {
        return 'main_image_path'.($locale === 'en' ? '' : '_'.$locale);
    }

    private function subImagesField(string $locale): string
    {
        return 'sub_images'.($locale === 'en' ? '' : '_'.$locale);
    }

    private function storeImage(UploadedFile $image, string $directory): string
    {
        $path = $image->store($directory, 'public');

        if (! is_string($path)) {
            throw new RuntimeException('Unable to store a news image.');
        }

        return $path;
    }
}
