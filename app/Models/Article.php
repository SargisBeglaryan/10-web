<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'author',
        'original_content',
        'content_text',
        'article_date',
        'scraped_date',
        'excerpt',
    ];

    protected $casts = [
        'original_content' => 'array'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
