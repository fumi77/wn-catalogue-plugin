<?php

namespace Smart\Catalogue\Components;

use BackendAuth;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Lang;
use Redirect;
use Smart\Catalogue\Models\Category as CatalogueCategory;
use Smart\Catalogue\Models\Post as CataloguePost;
use Smart\Catalogue\Models\Settings as CatalogueSettings;
use Winter\Storm\Database\Collection;
use Winter\Storm\Database\Model;

class Posts extends ComponentBase
{
    /**
     * A collection of posts to display
     */
    public LengthAwarePaginator|Collection|null $posts;

    /**
     * Parameter to use for the page number
     */
    public ?string $pageParam;

    /**
     * If the post list should be filtered by a category, the model to use
     */
    public ?CatalogueCategory $category;

    /**
     * Message to display when there are no messages
     */
    public ?string $noPostsMessage;

    /**
     * Reference to the page name for linking to posts
     */
    public ?string $postPage;

    /**
     * Reference to the page name for linking to categories
     */
    public ?string $categoryPage;

    /**
     * If the post list should be ordered by another attribute
     */
    public ?string $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'smart.catalogue::lang.settings.posts_title',
            'description' => 'smart.catalogue::lang.settings.posts_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'smart.catalogue::lang.settings.posts_pagination',
                'description' => 'smart.catalogue::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'smart.catalogue::lang.settings.posts_filter',
                'description' => 'smart.catalogue::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => '',
            ],
            'postsPerPage' => [
                'title'             => 'smart.catalogue::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'smart.catalogue::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'             => 'smart.catalogue::lang.settings.posts_no_posts',
                'description'       => 'smart.catalogue::lang.settings.posts_no_posts_description',
                'type'              => 'string',
                'default'           => Lang::get('smart.catalogue::lang.settings.posts_no_posts_default'),
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'       => 'smart.catalogue::lang.settings.posts_order',
                'description' => 'smart.catalogue::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
            ],
            'categoryPage' => [
                'title'       => 'smart.catalogue::lang.settings.posts_category',
                'description' => 'smart.catalogue::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'catalogue/category',
                'group'       => 'smart.catalogue::lang.settings.group_links',
            ],
            'postPage' => [
                'title'       => 'smart.catalogue::lang.settings.posts_post',
                'description' => 'smart.catalogue::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'catalogue/post',
                'group'       => 'smart.catalogue::lang.settings.group_links',
            ],
            'exceptPost' => [
                'title'             => 'smart.catalogue::lang.settings.posts_except_post',
                'description'       => 'smart.catalogue::lang.settings.posts_except_post_description',
                'type'              => 'string',
                'validationPattern' => '^[a-z0-9\-_,\s]+$',
                'validationMessage' => 'smart.catalogue::lang.settings.posts_except_post_validation',
                'default'           => '',
                'group'             => 'smart.catalogue::lang.settings.group_exceptions',
            ],
            'exceptCategories' => [
                'title'             => 'smart.catalogue::lang.settings.posts_except_categories',
                'description'       => 'smart.catalogue::lang.settings.posts_except_categories_description',
                'type'              => 'string',
                'validationPattern' => '^[a-z0-9\-_,\s]+$',
                'validationMessage' => 'smart.catalogue::lang.settings.posts_except_categories_validation',
                'default'           => '',
                'group'             => 'smart.catalogue::lang.settings.group_exceptions',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions()
    {
        $options = CataloguePost::$allowedSortingOptions;

        foreach ($options as $key => $value) {
            $options[$key] = Lang::get($value);
        }

        return $options;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1) {
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
            }

            if (!is_null($currentPage) && $currentPage < 1) {
                return Redirect::to($this->currentPageUrl([$pageNumberParam => 1]));
            }
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        /*
         * Page links
         */
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $category = $this->category ? $this->category->id : null;
        $categorySlug = $this->category ? $this->category->slug : null;

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !$this->checkEditor();

        $posts = CataloguePost::with(['categories', 'featured_images'])->listFrontEnd([
            'page'             => $this->property('pageNumber'),
            'sort'             => $this->property('sortOrder'),
            'perPage'          => $this->property('postsPerPage'),
            'search'           => trim(input('search')),
            'category'         => $category,
            'published'        => $isPublished,
            'exceptPost'       => is_array($this->property('exceptPost'))
                ? $this->property('exceptPost')
                : preg_split('/,\s*/', $this->property('exceptPost'), -1, PREG_SPLIT_NO_EMPTY),
            'exceptCategories' => is_array($this->property('exceptCategories'))
                ? $this->property('exceptCategories')
                : preg_split('/,\s*/', $this->property('exceptCategories'), -1, PREG_SPLIT_NO_EMPTY),
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) use ($categorySlug) {
            $post->setUrl($this->postPage, $this->controller, ['category' => $categorySlug]);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }

    protected function loadCategory()
    {
        if (!$slug = $this->property('categoryFilter')) {
            return null;
        }

        $category = new CatalogueCategory;

        $category = $category->isClassExtendedWith('Winter.Translate.Behaviors.TranslatableModel')
            ? $category->transWhere('slug', $slug)
            : $category->where('slug', $slug);

        $category = $category->first();

        return $category ?: null;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();

        return $backendUser && $backendUser->hasAccess('smart.catalogue.access_posts') && CatalogueSettings::get('show_all_posts', true);
    }
}
