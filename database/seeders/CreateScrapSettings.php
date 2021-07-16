<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ScraperSettings;

class CreateScrapSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = date("Y");
        $currentYearStart = Carbon::createFromDate($year, 1, 1);
        $currentYearEnd = Carbon::createFromDate($year, 12, 31);

        ScraperSettings::create([
            'limit' => 50,
            'start_date' => $currentYearStart,
            'end_date' => $currentYearEnd,
        ]);


    }
}
