<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'actor_name',
        'action',
        'subject_type',
        'subject_id',
        'subject_name',
        'details',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
