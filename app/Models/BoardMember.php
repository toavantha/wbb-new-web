<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardMember extends Model
{
    protected $fillable = [
        'name',
        'name_si',
        'name_ta',
        'position',
        'email',
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
        $locale = in_array(app()->getLocale(), ['si', 'ta'], true) ? app()->getLocale() : 'en';

        return $this->{'name_'.$locale} ?: $this->name;
    }

    public function imageUrl(): ?string
    {
        if ($this->image_path === null) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
