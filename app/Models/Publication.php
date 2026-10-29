<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'document_number',
        'language',
        'description',
        'file_path',
        'file_url',
        'file_size',
        'published_date',
        'is_active',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the download URL for the publication.
     */
    public function getFileUrlAttribute(?string $value): ?string
    {
        if ($this->file_path && $this->exists) {
            return route('publications.download', $this->id);
        }

        return $value;
    }
}
