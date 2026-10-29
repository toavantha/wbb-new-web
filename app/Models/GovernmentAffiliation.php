<?php

namespace App\Models;

use Database\Factories\GovernmentAffiliationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GovernmentAffiliation extends Model
{
    /** @use HasFactory<GovernmentAffiliationFactory> */
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_si',
        'name_ta',
        'title_en',
        'title_si',
        'title_ta',
        'ministry_en',
        'ministry_si',
        'ministry_ta',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function localizedName(): string
    {
        return $this->localizedValue('name');
    }

    public function localizedTitle(): string
    {
        return $this->localizedValue('title');
    }

    public function localizedMinistry(): string
    {
        return $this->localizedValue('ministry');
    }

    public function imageUrl(): ?string
    {
        if ($this->image_path === null) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        if (Str::startsWith($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return Storage::disk('public')->url($this->image_path);
    }

    private function localizedValue(string $field): string
    {
        $locale = in_array(app()->getLocale(), ['en', 'si', 'ta'], true) ? app()->getLocale() : 'en';

        return $this->{$field.'_'.$locale} ?: $this->{$field.'_en'};
    }
}
