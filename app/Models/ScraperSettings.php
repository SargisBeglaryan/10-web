<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScraperSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'limit',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getFormattedDateRange() {
        $startDate = new Carbon($this->start_date);
        $endDate = new Carbon($this->end_date);
        
        return $startDate->format('m/d/Y') .' - '. $endDate->format('m/d/Y');
    }
}
