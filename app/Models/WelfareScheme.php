<?php

namespace App\Models;

use Database\Factories\WelfareSchemeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareScheme extends Model
{
    /** @use HasFactory<WelfareSchemeFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'monthly_allowance',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'monthly_allowance' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array{name: string, badge: string, duration: string, description: string, indicators: array<int, string>}
     */
    public function localizedContent(): array
    {
        $locale = in_array(app()->getLocale(), ['en', 'si', 'ta'], true) ? app()->getLocale() : 'en';
        $content = $this->content;
        $localizedContent = $content[$locale] ?? $content['en'];

        return [
            'name' => $localizedContent['name'],
            'badge' => $localizedContent['badge'],
            'duration' => $localizedContent['duration'],
            'description' => $localizedContent['description'],
            'indicators' => $localizedContent['indicators'],
        ];
    }

    /**
     * @return array{name: string, badge: string, duration: string, description: string, indicators: array<int, string>, allowance: string}
     */
    public function publicCard(): array
    {
        return [
            ...$this->localizedContent(),
            'allowance' => 'Rs. '.number_format($this->monthly_allowance).' / month',
        ];
    }
}
