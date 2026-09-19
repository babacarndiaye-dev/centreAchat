<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name', 'author_role', 'content', 'rating', 'photo', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
