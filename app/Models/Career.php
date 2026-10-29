<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    public const DOCUMENT_TYPES = ['tor', 'details', 'application'];

    public const DOCUMENT_LOCALES = ['en', 'si', 'ta'];

    protected $fillable = [
        'title',
        'department',
        'employment_type',
        'location',
        'salary_scale',
        'description',
        'qualifications',
        'closing_date',
        'attachment_url',
        'tor_file_path',
        'details_file_path',
        'application_file_path',
        'tor_si_file_path',
        'details_si_file_path',
        'application_si_file_path',
        'tor_ta_file_path',
        'details_ta_file_path',
        'application_ta_file_path',
        'is_active',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'is_active' => 'boolean',
    ];
}
