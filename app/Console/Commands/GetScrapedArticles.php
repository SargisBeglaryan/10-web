<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\ScraperSettings;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GetScrapedArticles extends Command
{

    protected $scraperSettings;
    protected $pagesCount = 0;
    protected $addedPostsCount = 0;
    protected $websiteUrl = 'https://10web.io/blog/';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:scraped-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Scraped Articles by Guzzle from external website';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->scraperSettings = ScraperSettings::orderByDesc('id')->first();
            $addedArticlesCount = 0;

            $client = new Client();
            $request = $client->get($this->websiteUrl);
            $response = $request->getBody();

            $DOM = new \DOMDocument('1.0', 'utf-8');
            @$DOM->loadHTML($response->getContents());

            $this->getPagesCount($DOM);

            $this->addPageBlogPosts($DOM);

            $this->addAllPagesArticles();
            
            echo 'Success';
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function addAllPagesArticles() {

        for ($i = 2; $i <= $this->pagesCount; $i++) {
            if ($this->scraperSettings->limit < $this->addedPostsCount) {
                break;
            }

            $websiteCurrentUrl = $this->websiteUrl. '/page/'. $i . '/';

            $client = new Client();
            $request = $client->get($websiteCurrentUrl);
            $response = $request->getBody();

            $DOM = new \DOMDocument('1.0', 'utf-8');
            @$DOM->loadHTML($response->getContents());
            $this->addPageBlogPosts($DOM);

        }
    }

    protected function getPagesCount($dom): void {
        $finder = new \DomXPath($dom);
        $classname="page-numbers";
        $nodes = $finder->query("//*[contains(@class, '$classname')]");
        $this->pagesCount = intval($nodes->item($nodes->length - 2)->nodeValue);
    }

    protected function addPageBlogPosts($dom) {
        $finder = new \DomXPath($dom);
        $classname = "blog-post post";
        $pagePostsList = $finder->query("//*[contains(@class, '$classname')]");

        for ($i = 0; $i < $pagePostsList->length; $i++) {
            if ($this->scraperSettings->limit < $this->addedPostsCount) {
                break;
            }

            $this->checkAndAddBlogPost($pagePostsList[$i]);
        }
    }

    protected function checkAndAddBlogPost($pagePosts) {
        $postLink = $pagePosts->getElementsByTagName('a')->item(0)->getAttribute('href');
        var_dump($postLink);
        $client = new Client();
        $request = $client->get($postLink);
        $response = $request->getBody();

        $DOM = new \DOMDocument('1.0', 'utf-8');
        @$DOM->loadHTML($response->getContents());
        
        if ($DOM->getElementsByTagName('h1')->length == 0) {
            return false;
        }

        $allArticlesTitles = Article::pluck('title')->toArray();
        $title = $DOM->getElementsByTagName('h1')->item(0)->nodeValue;

        if (!in_array($title, $allArticlesTitles)) {
            $articleDate = $this->checkAndGetArticleDate($DOM);
            if ($articleDate) {
                $newArticle = new Article();
                $newArticle->title = $title;
                $newArticle->author = $this->getNewArticleAuthor($DOM);
                $newArticle->original_content = $this->getNewArticleOriginalContent($DOM);
                //$newArticle->content_text = $this->getNewArticleContentText($DOM);
                $newArticle->content_text = null;
                $newArticle->article_date = $articleDate;
                $newArticle->scraped_date = Carbon::now();
                $newArticle->excerpt = $this->getNewArticleCategory($DOM);
                $newArticle->image = $this->getNewArticleImage($DOM);
                $newArticle->created_at = Carbon::now();
                $newArticle->updated_at = Carbon::now();
                $newArticle->save();
                $this->addedPostsCount = $this->addedPostsCount + 1;
            }
        }
    }

    protected function getNewArticleAuthor($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="author";
        $authorContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($authorContent->length == 0) {
            return null;
        }

        return $authorContent->item(0)->getElementsByTagName('a')->item(0)->nodeValue;
    }

    protected function getNewArticleOriginalContent($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="entry-content";
        $articleContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($articleContent->length == 0) {
            return null;
        }

        return $articleContent->item(0)->nodeValue ?? null;
    }

    protected function getNewArticleCategory($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="category";
        $categoryContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($categoryContent->length == 0) {
            return null;
        }

        return $categoryContent->item(0)->getElementsByTagName('a')->item(0)->nodeValue;
    }

    protected function getNewArticleImage($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="wp-post-image";
        $imageContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($imageContent->length == 0) {
            return null;
        }
        
        $imagePath = $imageContent->item(0)->getAttribute('src') ?? null;
        
        
        $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
        $fileName = '10-web-'. time(). '.'.$ext;
        $getImageContent = file_get_contents($imagePath);
        Storage::disk('public')->put('images/' . $fileName, $getImageContent);
        return 'images/'.$fileName;
    }

    protected function checkAndGetArticleDate($dom) {

        if (!$dom->getElementsByTagName('time')->length) {
            return false;
        }
        $articleDate = new Carbon($dom->getElementsByTagName('time')->item(0)->getAttribute('datetime')) ?? null;

        $startDate = new Carbon($this->scraperSettings->start_date);
        $endDate = new Carbon($this->scraperSettings->end_date);
 
        if ($articleDate->between($startDate, $endDate)) {
            return $articleDate->format('Y-m-d');
        }

        return false;
    }


}
