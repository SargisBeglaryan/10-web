<?php


namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Models\ScrapperSettings;
use Auth;
use Illuminate\Http\Request;

class DashboardService implements DashboardRepository {

    public function updateScrapperSettings(Request $request, int $id): ?ScrapperSettings {
    	return null;
    }
}
