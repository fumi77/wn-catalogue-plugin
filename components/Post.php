<?php

namespace Winter\Catalogue\Components;

use BackendAuth;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Event;
use Winter\Catalogue\Models\Post as CataloguePost;

class Post extends ComponentBase
{
    /**
     * The post model used for display.
     */
    public ?CataloguePost $post = null;

    /**
     * Reference to the page name for linking to categories.
     */
    public ?string $categoryPage = '';

    public function componentDetails(): array
    {
        return [
            'name'        => 'winter.catalogue::lang.settings.post_title',
            'description' => 'winter.catalogue::lang.settings.post_description'
        ];
    }

    public function defineProperties(): array
    {
        return [
            'slug' => [
                'title'       => 'winter.catalogue::lang.settings.post_slug',
                'description' => 'winter.catalogue::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'categoryPage' => [
                'title'       => 'winter.catalogue::lang.settings.post_category',
                'description' => 'winter.catalogue::lang.settings.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'catalogue/category',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        Event::listen('translate.localePicker.translateParams', function ($page, $params, $oldLocale, $newLocale) {
            $newParams = $params;

            if (isset($params['slug'])) {
                $records = CataloguePost::transWhere('slug', $params['slug'], $oldLocale)->first();
                if ($records) {
                    $records->translateContext($newLocale);
                    $newParams['slug'] = $records['slug'];
                }
            }

            return $newParams;
        });
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->post = $this->page['post'] = $this->loadPost();
        if (!$this->post) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }
    }

    public function onRender()
    {
        if (empty($this->post)) {
            $this->post = $this->page['post'] = $this->loadPost();
        }
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');

        $post = new CataloguePost;
        $query = $post->query();

        if ($post->isClassExtendedWith('Winter.Translate.Behaviors.TranslatableModel')) {
            $query->transWhere('slug', $slug);
        } else {
            $query->where('slug', $slug);
        }

        if (!$this->checkEditor()) {
            $query->isPublished();
        }

        $post = $query->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->exists && $post->categories->count()) {
            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        return $post;
    }

    public function previousPost()
    {
        return $this->getPostSibling(-1);
    }

    public function nextPost()
    {
        return $this->getPostSibling(1);
    }

    protected function getPostSibling($direction = 1)
    {
        if (!$this->post) {
            return;
        }

        $method = $direction === -1 ? 'previousPost' : 'nextPost';

        if (!$post = $this->post->$method()) {
            return;
        }

        $postPage = $this->getPage()->getBaseFileName();

        $post->setUrl($postPage, $this->controller);

        $post->categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);
        });

        return $post;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();

        return $backendUser && $backendUser->hasAccess('winter.catalogue.access_posts');
    }
}
