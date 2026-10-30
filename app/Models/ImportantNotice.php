<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportantNotice extends Model
{
    protected $fillable = [
        'message_en',
        'message_si',
        'message_ta',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function localizedMessage(): string
    {
        $locale = app()->getLocale();
        $locale = in_array($locale, ['en', 'si', 'ta'], true) ? $locale : 'en';

        return $this->{'message_'.$locale};
    }
}
