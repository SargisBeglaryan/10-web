<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Repositories\ArticlesRepository;
use Illuminate\Support\Facades\Cache;

class ArticlesController extends Controller
{
    protected $articlesService;

    public function __construct(ArticlesRepository $articlesService) {
        $this->articlesService = $articlesService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $url = $request->fullUrl();

        if (Cache::has($url)) {
            return Cache::get($url);
        }
        
        $articles = $this->articlesService->getArticlesList($request);

        return Cache::rememberForever($url, function () use ($articles) {
            return view('home.index')->with(compact('articles'))->render();
        });
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $url = $request->fullUrl();
        
        if (Cache::has($url)) {
            return Cache::get($url);
        }
        
        $article = $this->articlesService->getArticleById($id);

        return Cache::rememberForever($url, function () use ($article) {
            return view('home.show')->with(compact('article'))->render();
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //
    }
}
