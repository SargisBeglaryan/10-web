<?php


namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\ScraperSettings;
use Illuminate\Support\Collection;

interface DashboardRepository {

    public function updateScraperSettings(Request $request, int $id): ?ScraperSettings;

    public function getScraperSettings(): ?ScraperSettings;

}
