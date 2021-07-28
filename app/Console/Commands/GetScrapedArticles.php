<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\ScraperSettings;
use App\Models\Article;
use App\Models\MostUsedWord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GetScrapedArticles extends Command
{

    protected $scraperSettings;
    protected $mostUsedWords = [];
    protected $pagesCount = 0;
    protected $addedPostsCount = 0;
    protected $websiteUrl = 'https://10web.io/blog/';
    protected $out;
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
        $this->out = new \Symfony\Component\Console\Output\ConsoleOutput();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $this->scraperSettings = ScraperSettings::orderByDesc('id')->first();
            $addedArticlesCount = 0;

            $client = new Client();
            $request = $client->get($this->websiteUrl);
            $response = $request->getBody();

            $DOM = new \DOMDocument('1.0', 'utf-8');
            @$DOM->loadHTML($response->getContents());

            $this->getPagesCount($DOM);

            $this->addPageBlogPosts($DOM);

            $this->out->writeln("Page 1 scraped...........");

            $this->addAllPagesArticles();

            $this->addDayMostUsedWord();

            DB::commit();
            $this->out->writeln('Success');
        } catch (\Exception $ex) {
            DB::rollBack();
            $this->out->writeln($ex->getMessage());
        }
    }

    protected function addDayMostUsedWord() {
        if (!empty($this->mostUsedWords)) {
            $keyName = array_key_first($this->mostUsedWords);
            $mostUsedWord = new MostUsedWord();
            $mostUsedWord->name = $keyName;
            $mostUsedWord->count = $this->mostUsedWords[$keyName];
            $mostUsedWord->date = Carbon::now();
            $mostUsedWord->save();
        }

    }

    protected function addAllPagesArticles() {

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
            $this->out->writeln('Page '. $i . ' scraped...........');

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
                $newArticle->content_text = $this->getNewArticleContentText($DOM);
                $newArticle->article_date = $articleDate;
                $newArticle->scraped_date = Carbon::now();
                $newArticle->excerpt = $this->getNewArticleCategory($DOM);
                $newArticle->image = $this->getNewArticleImage($DOM);
                $newArticle->created_at = Carbon::now();
                $newArticle->updated_at = Carbon::now();
                $newArticle->save();

                Cache::flush();
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

        $parsedHtml = trim(str_replace('"', "'", preg_replace("/[\r\n]+/", "", $dom->saveHTML($articleContent->item(0)))));

        return json_encode(str_replace("\/", "/", str_replace("data-src", "src", $parsedHtml)));
    }

    protected function getNewArticleContentText($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="entry-content";
        $articleContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($articleContent->length == 0) {
            return null;
        }

        $articleContentText = strip_tags($articleContent->item(0)->nodeValue);

        $this->getArticleMostUsedWords($articleContentText);

        return $articleContentText;
    }

    protected function getNewArticleCategory($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname = 'post_info_container';
        $categoryBlock = $finder->query("//div[contains(@class, '$classname')]");

        if ($categoryBlock->length == 0) {
            return null;
        }

        $contentElements = $categoryBlock->item(0)->getElementsByTagName('span');

        $categoryContent = $contentElements->item(1)->getElementsByTagName('a');

        $catagories = [];

        for ($i = 0; $i < $categoryContent->length; $i++) {
            $catagories[] = $categoryContent->item($i)->nodeValue;
        }

        return implode(',', $catagories);
    }

    protected function getNewArticleImage($dom): ?string {
        $finder = new \DomXPath($dom);
        $classname="wp-post-image";
        $imageContent = $finder->query("//*[contains(@class, '$classname')]");

        if ($imageContent->length == 0) {
            return null;
        }
        
        $imagePath = $imageContent->item(0)->getAttribute('src');
               
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

    protected function getArticleMostUsedWords(string $content_text): void {

        $words = array_filter(explode(' ', $content_text), function($val){
            return strtolower(strlen($val)) > 4;
        });

        foreach ($words as $word) {
            if ($word == '') {
                continue;
            }

            array_key_exists( $word, $this->mostUsedWords ) ? $this->mostUsedWords[ $word ]++ : $this->mostUsedWords[ $word ] = 1;

        }

        arsort($this->mostUsedWords);

    }


}
