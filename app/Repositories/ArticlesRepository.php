<?php


namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Article;

interface ArticlesRepository {

    public function getArticlesList(Request $request): ?LengthAwarePaginator;

    public function getArticleById(int $id): ?Article;

}
