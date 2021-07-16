<?php


namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Models\ScraperSettings;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardService implements DashboardRepository {

    public function updateScraperSettings(Request $request, int $id): ?ScraperSettings {
    	$scraperSettings = ScraperSettings::find($id);

        if ($scraperSettings == null) {
            $scraperSettings = new ScraperSettings();
        }

        $rangeDate = explode(" - ", $request->daterange);

        $scraperSettings->limit = $request->limit;
        $scraperSettings->start_date = Carbon::parse($rangeDate[0])->format('Y-m-d');
        $scraperSettings->end_date = Carbon::parse($rangeDate[1])->format('Y-m-d');
        $scraperSettings->saveOrFail();

        return $scraperSettings;
    }

    public function getScraperSettings(): ?ScraperSettings {
        return ScraperSettings::orderByDesc('id')->first();
    }
}
