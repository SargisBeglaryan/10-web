<?php


namespace App\Services;

use App\Repositories\ArticlesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\Article;
use Carbon\Carbon;

class ArticlesService implements ArticlesRepository {

    public function getArticlesList(Request $request): ?LengthAwarePaginator {
    	$query = Article::query();

        if ($request->date) {
            $query->where('article_date', $request->date);
        }

        if ($request->keyword) {
            $query->where('title','LIKE', '%'.$request->keyword.'%');
        }

        $query->orderByDesc('id');

        return $query->paginate(6);
    }
}
