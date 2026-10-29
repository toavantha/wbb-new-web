<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_si',
        'name_ta',
        'designation',
        'designation_si',
        'designation_ta',
        'mobile',
        'email',
        'image_path',
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

    public function localizedDesignation(): string
    {
        $locale = in_array(app()->getLocale(), ['si', 'ta'], true) ? app()->getLocale() : 'en';

        return $this->{'designation_'.$locale} ?: $this->designation;
    }
}
