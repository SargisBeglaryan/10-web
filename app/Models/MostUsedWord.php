<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MostUsedWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'count',
        'date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
