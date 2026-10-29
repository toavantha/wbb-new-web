<?php

namespace App\Models;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NewsItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_si',
        'title_ta',
        'slug',
        'category',
        'excerpt',
        'excerpt_si',
        'excerpt_ta',
        'content',
        'content_si',
        'content_ta',
        'image_url',
        'main_image_path',
        'main_image_path_si',
        'main_image_path_ta',
        'published_date',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(NewsImage::class)->orderBy('sort_order');
    }

    public function localized(string $attribute, ?string $locale = null): ?string
    {
        if (! in_array($attribute, ['title', 'excerpt', 'content'], true)) {
            throw new InvalidArgumentException('Unsupported localized news attribute.');
        }

        $locale ??= app()->getLocale();

        if (in_array($locale, ['si', 'ta'], true)) {
            $translation = $this->getAttribute($attribute.'_'.$locale);

            if (is_string($translation) && trim($translation) !== '') {
                return $translation;
            }
        }

        $englishContent = $this->getAttribute($attribute);

        return is_string($englishContent) ? $englishContent : null;
    }

    public function localizedMainImageUrl(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $localizedPath = in_array($locale, ['si', 'ta'], true)
            ? $this->getAttribute('main_image_path_'.$locale)
            : null;
        $imagePath = is_string($localizedPath) && $localizedPath !== ''
            ? $localizedPath
            : $this->main_image_path;

        if ($imagePath) {
            return Storage::disk('public')->url($imagePath);
        }

        return $this->image_url;
    }

    /**
     * @return Collection<int, NewsImage>
     */
    public function localizedImages(?string $locale = null): Collection
    {
        $locale ??= app()->getLocale();

        if (in_array($locale, ['si', 'ta'], true)) {
            $translatedImages = $this->images->where('locale', $locale);

            if ($translatedImages->isNotEmpty()) {
                return $translatedImages;
            }
        }

        return $this->images->where('locale', 'en');
    }

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->localizedMainImageUrl('en');
    }
}
