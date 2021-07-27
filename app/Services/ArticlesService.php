<?php


namespace App\Services;

use App\Repositories\ArticlesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\MostUsedWord;
use Carbon\Carbon;

class ArticlesService implements ArticlesRepository {

    public function getArticlesList(Request $request): ?LengthAwarePaginator {
        $mostUsedWord = MostUsedWord::orderByDesc('id')->first();

    	$query = Article::query();

        if ($request->date) {
            $query->where('article_date', $request->date);
        }

        if ($request->keyword) {
            $query->where('title','LIKE', '%'.$request->keyword.'%');
        }

        if ($mostUsedWord != null) {

        } else {
            $query->orderByDesc('id');
        }


        return $query->paginate(6);
    }
}
