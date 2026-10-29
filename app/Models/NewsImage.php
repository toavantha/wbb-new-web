<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class NewsImage extends Model
{
    protected $fillable = [
        'image_path',
        'locale',
        'sort_order',
    ];

    public function newsItem(): BelongsTo
    {
        return $this->belongsTo(NewsItem::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
