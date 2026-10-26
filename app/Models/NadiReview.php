<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NadiReview extends Model
{
    protected $table = 'nadi_reviews';

    protected $fillable = [
        'patient_key', 'patient_label', 'rating', 'service_type',
        'service_name', 'focus_category', 'review', 'status', 'published_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'published_at' => 'datetime',
    ];
}
