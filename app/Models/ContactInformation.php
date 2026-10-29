<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
    protected $fillable = [
        'address',
        'hotline',
        'hotline_hours_en',
        'hotline_hours_si',
        'hotline_hours_ta',
        'office_phone',
        'email_primary',
        'email_secondary',
        'map_embed_url',
        'google_maps_url',
        'latitude',
        'longitude',
    ];

    public function localizedHotlineHours(): string
    {
        $locale = in_array(app()->getLocale(), ['en', 'si', 'ta'], true) ? app()->getLocale() : 'en';

        return $this->{'hotline_hours_'.$locale} ?: $this->hotline_hours_en;
    }
}
