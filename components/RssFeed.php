<?php

namespace Smart\Catalogue\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Lang;
use Response;
use Smart\Catalogue\Models\Category as CatalogueCategory;
use Smart\Catalogue\Models\Post as CataloguePost;
use Winter\Storm\Router\UrlGenerator;

class RssFeed extends ComponentBase
{
    /**
     * A collection of posts to display
     */
    public ?LengthAwarePaginator $posts;

    /**
     * If the post list should be filtered by a category, the model to use.
     */
    public ?CatalogueCategory $category;

    /**
     * Reference to the page name for the main catalogue page.
     */
    public ?string $cataloguePage;

    /**
     * Reference to the page name for linking to posts.
     */
    public ?string $postPage;

    public function componentDetails(): array
    {
        return [
            'name'        => 'smart.catalogue::lang.settings.rssfeed_title',
            'description' => 'smart.catalogue::lang.settings.rssfeed_description'
        ];
    }

    public function defineProperties(): array
    {
        return [
            'categoryFilter' => [
                'title'       => 'smart.catalogue::lang.settings.posts_filter',
                'description' => 'smart.catalogue::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => '',
            ],
            'sortOrder' => [
                'title'       => 'smart.catalogue::lang.settings.posts_order',
                'description' => 'smart.catalogue::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'created_at desc',
            ],
            'postsPerPage' => [
                'title'             => 'smart.catalogue::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'smart.catalogue::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'cataloguePage' => [
                'title'       => 'smart.catalogue::lang.settings.rssfeed_catalogue',
                'description' => 'smart.catalogue::lang.settings.rssfeed_catalogue_description',
                'type'        => 'dropdown',
                'default'     => 'catalogue/post',
                'group'       => 'smart.catalogue::lang.settings.group_links',
            ],
            'postPage' => [
                'title'       => 'smart.catalogue::lang.settings.posts_post',
                'description' => 'smart.catalogue::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'catalogue/post',
                'group'       => 'smart.catalogue::lang.settings.group_links',
            ],
        ];
    }

    public function getCataloguePageOptions(): array
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions(): array
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions(): array
    {
        $options = CataloguePost::$allowedSortingOptions;

        foreach ($options as $key => $value) {
            $options[$key] = Lang::get($value);
        }

        return $options;
    }

    public function onRun(): HttpResponse
    {
        $this->prepareVars();

        $xmlFeed = $this->renderPartial('@default');

        return Response::make($xmlFeed, '200')->header('Content-Type', 'text/xml');
    }

    protected function prepareVars(): void
    {
        $this->cataloguePage = $this->page['cataloguePage'] = $this->property('cataloguePage');
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        $this->page['link'] = $this->pageUrl($this->cataloguePage);
        $this->page['rssLink'] = url()->full();

        $currentPage = $this->posts->currentPage();
        $lastPage = $this->posts->lastPage();
        $prevPage = $currentPage > 1 ? $currentPage - 1 : null;
        $nextPage = $currentPage < $lastPage ? $currentPage + 1 : null;
        $this->page['paginationLinks'] = [
            'first' => $this->getPageUrl(1),
            'last'  => $this->getPageUrl($lastPage),
            'prev'  => $prevPage ? $this->getPageUrl($prevPage) : null,
            'next'  => $nextPage ? $this->getPageUrl($nextPage) : null,
        ];
    }

    /**
     * Get the URL to the provided page number
     */
    protected function getPageUrl(int $page): string
    {
        return UrlGenerator::buildUrl(
            url()->full(),
            ['query' => ['page' => $page]],
            HTTP_URL_JOIN_QUERY
        );
    }

    protected function listPosts(): LengthAwarePaginator
    {
        $category = $this->category ? $this->category->id : null;

        /*
         * List all the posts, eager load their categories
         */
        $posts = CataloguePost::with('categories')->listFrontEnd([
            'sort'     => $this->property('sortOrder'),
            'perPage'  => $this->property('postsPerPage'),
            'category' => $category,
            'page'     => (int) get('page', 1),
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);
        });

        return $posts;
    }

    protected function loadCategory(): ?CatalogueCategory
    {
        if (!$categoryId = $this->property('categoryFilter')) {
            return null;
        }

        if (!$category = CatalogueCategory::whereSlug($categoryId)->first()) {
            return null;
        }

        return $category;
    }
}
