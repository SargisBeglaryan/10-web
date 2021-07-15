<?php


namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\ScrapperSettings;
use Illuminate\Support\Collection;

interface DashboardRepository {

    public function updateScrapperSettings(Request $request, int $id): ?ScrapperSettings;

}
