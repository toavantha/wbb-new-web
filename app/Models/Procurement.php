<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ProcurementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Procurement extends Model
{
    /** @use HasFactory<ProcurementFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'Procurement Notice',
        'Tender Document',
        'TOR',
        'Other',
    ];

    public const LOCALES = ['en', 'si', 'ta'];

    protected $fillable = [
        'title',
        'title_si',
        'title_ta',
        'reference_no',
        'category',
        'description',
        'description_si',
        'description_ta',
        'file_path',
        'file_path_si',
        'file_path_ta',
        'file_size',
        'file_size_si',
        'file_size_ta',
        'published_date',
        'deadline_date',
        'original_deadline_date',
        'is_active',
    ];

    protected $casts = [
        'published_date' => 'date',
        'deadline_date' => 'date',
        'original_deadline_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the title in the requested or active locale, falling back to English.
     */
    public function localizedTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'si' && ! empty($this->title_si)) {
            return $this->title_si;
        }

        if ($locale === 'ta' && ! empty($this->title_ta)) {
            return $this->title_ta;
        }

        return $this->title;
    }

    /**
     * Get the description in the requested or active locale, falling back to English.
     */
    public function localizedDescription(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'si' && ! empty($this->description_si)) {
            return $this->description_si;
        }

        if ($locale === 'ta' && ! empty($this->description_ta)) {
            return $this->description_ta;
        }

        return $this->description;
    }

    /**
     * Get the file path in the requested or active locale, falling back to English.
     */
    public function localizedFilePath(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'si' && ! empty($this->file_path_si)) {
            return $this->file_path_si;
        }

        if ($locale === 'ta' && ! empty($this->file_path_ta)) {
            return $this->file_path_ta;
        }

        return $this->file_path;
    }

    /**
     * Get the file size in the requested or active locale, falling back to English.
     */
    public function localizedFileSize(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'si' && ! empty($this->file_path_si)) {
            return $this->file_size_si ?: $this->file_size;
        }

        if ($locale === 'ta' && ! empty($this->file_path_ta)) {
            return $this->file_size_ta ?: $this->file_size;
        }

        return $this->file_size;
    }

    /**
     * Check whether a specific language has dedicated content uploaded.
     */
    public function hasLanguageContent(string $locale): bool
    {
        if ($locale === 'en') {
            return ! empty($this->title) || ! empty($this->file_path);
        }

        if ($locale === 'si') {
            return ! empty($this->title_si) || ! empty($this->file_path_si);
        }

        if ($locale === 'ta') {
            return ! empty($this->title_ta) || ! empty($this->file_path_ta);
        }

        return false;
    }

    /**
     * Check whether a specific language file exists on disk.
     */
    public function hasFileForLocale(string $locale): bool
    {
        $path = match ($locale) {
            'si' => $this->file_path_si,
            'ta' => $this->file_path_ta,
            default => $this->file_path,
        };

        return ! empty($path) && Storage::disk('public')->exists($path);
    }

    /**
     * Check if the procurement notice deadline has passed.
     */
    public function isExpired(): bool
    {
        if (! $this->deadline_date) {
            return false;
        }

        return $this->deadline_date->lt(Carbon::today());
    }

    /**
     * Check if the deadline was extended.
     */
    public function isExtended(): bool
    {
        return $this->original_deadline_date !== null && $this->deadline_date->gt($this->original_deadline_date);
    }
}
