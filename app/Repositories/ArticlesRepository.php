<?php


namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface ArticlesRepository {

    public function getArticlesList(Request $request): ?LengthAwarePaginator;

}
