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
        $mostUsedWord = MostUsedWord::select('name')->orderByDesc('id')->pluck('name')->first();

    	$query = Article::query();

        if ($mostUsedWord != null) {
            $query->selectRaw("id, title, article_date, image, content_text, ROUND((LENGTH(content_text) - LENGTH(REPLACE(content_text, '$mostUsedWord', ''))) / LENGTH('$mostUsedWord')) AS most_used_word_count");
            $query->where('content_text','LIKE', '%'.$mostUsedWord.'%');
        }

        if ($request->article_date) {
            $query->where('article_date', $request->article_date);
        }

        if ($request->keyword) {
            $query->where('title','LIKE', '%'.$request->keyword.'%');
        }

        if ($mostUsedWord != null) {
            $query->orderByDesc('most_used_word_count');
        } else {
            $query->orderByDesc('id');
        }

        return $query->paginate(6);
    }

    public function getArticleById(int $id): ?Article {
        return Article::findOrFail($id);
    }
}
