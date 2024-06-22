<?php

namespace Smart\Catalogue;

use Backend;
use Backend\Models\UserRole;
use Event;
use System\Classes\PluginBase;
use Smart\Catalogue\Classes\TagProcessor;
use Smart\Catalogue\Models\Category;
use Smart\Catalogue\Models\Post;

class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'smart.catalogue::lang.plugin.name',
            'description' => 'smart.catalogue::lang.plugin.description',
            'author'      => 'Winter CMS',
            'icon'        => 'icon-pencil',
            'homepage'    => 'hhttps://github.com/fumi77/wn-catalogue-plugin',
            'replaces'    => ['Smart.Catalogue' => '<= 1.0.1'],
        ];
    }

    /**
     * Registers the components provided by this plugin.
     */
    public function registerComponents(): array
    {
        return [
            \Smart\Catalogue\Components\Post::class       => 'cataloguePost',
            \Smart\Catalogue\Components\Posts::class      => 'cataloguePosts',
            \Smart\Catalogue\Components\Categories::class => 'catalogueCategories',
            \Smart\Catalogue\Components\RssFeed::class    => 'catalogueRssFeed'
        ];
    }

    /**
     * Registers the permissions provided by this plugin.
     */
    public function registerPermissions(): array
    {
        return [
            'smart.catalogue.manage_settings' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.manage_settings',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'smart.catalogue.access_posts' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.access_posts',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'smart.catalogue.access_categories' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.access_categories',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'smart.catalogue.access_other_posts' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.access_other_posts',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'smart.catalogue.access_import_export' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.access_import_export',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'smart.catalogue.access_publish' => [
                'tab'   => 'smart.catalogue::lang.catalogue.tab',
                'label' => 'smart.catalogue::lang.catalogue.access_publish',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ]
        ];
    }

    /**
     * Registers the backend navigation items provided by this plugin.
     */
    public function registerNavigation(): array
    {
        return [
            'catalogue' => [
                'label'       => 'smart.catalogue::lang.catalogue.menu_label',
                'url'         => Backend::url('smart/catalogue/posts'),
                'icon'        => 'icon-pencil',
                'iconSvg'     => 'plugins/smart/catalogue/assets/images/catalogue-icon.svg',
                'permissions' => ['smart.catalogue.*'],
                'order'       => 300,

                'sideMenu' => [
                    'new_post' => [
                        'label'       => 'smart.catalogue::lang.posts.new_post',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('smart/catalogue/posts/create'),
                        'permissions' => ['smart.catalogue.access_posts']
                    ],
                    'posts' => [
                        'label'       => 'smart.catalogue::lang.catalogue.posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('smart/catalogue/posts'),
                        'permissions' => ['smart.catalogue.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'smart.catalogue::lang.catalogue.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('smart/catalogue/categories'),
                        'permissions' => ['smart.catalogue.access_categories']
                    ]
                ]
            ]
        ];
    }

    /**
     * Registers the settings provided by this plugin.
     */
    public function registerSettings(): array
    {
        return [
            'catalogue' => [
                'label' => 'smart.catalogue::lang.catalogue.menu_label',
                'description' => 'smart.catalogue::lang.catalogue.settings_description',
                'category' => 'smart.catalogue::lang.catalogue.menu_label',
                'icon' => 'icon-pencil',
                'class' => 'Smart\Catalogue\Models\Settings',
                'order' => 500,
                'keywords' => 'catalogue post category',
                'permissions' => ['smart.catalogue.manage_settings']
            ]
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {
        /*
         * Register the image tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview) {
            if (!$preview) {
                return $input;
            }

            return preg_replace('|\<img src="image" alt="([0-9]+)"([^>]*)\/>|m',
                '<span class="image-placeholder" data-index="$1">
                    <span class="upload-dropzone">
                        <span class="label">Click or drop an image...</span>
                        <span class="indicator"></span>
                    </span>
                </span>',
            $input);
        });
    }

    /**
     * Boot method, called when the plugin is first booted.
     */
    public function boot(): void
    {
        $this->extendWinterPagesPlugin();
    }

    /**
     * Extends the Winter.Pages plugin
     */
    protected function extendWinterPagesPlugin(): void
    {
        /*
         * Register menu items for the Winter.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function () {
            return [
                'catalogue-category'       => 'smart.catalogue::lang.menuitem.catalogue_category',
                'all-catalogue-categories' => 'smart.catalogue::lang.menuitem.all_catalogue_categories',
                'catalogue-post'           => 'smart.catalogue::lang.menuitem.catalogue_post',
                'all-catalogue-posts'      => 'smart.catalogue::lang.menuitem.all_catalogue_posts',
                'category-catalogue-posts' => 'smart.catalogue::lang.menuitem.category_catalogue_posts',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function ($type) {
            switch ($type) {
                case 'catalogue-category':
                case 'all-catalogue-categories':
                    return Category::getMenuTypeInfo($type);
                case 'catalogue-post':
                case 'all-catalogue-posts':
                case 'category-catalogue-posts':
                    return Post::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            switch ($type) {
                case 'catalogue-category':
                case 'all-catalogue-categories':
                    return Category::resolveMenuItem($item, $url, $theme);
                case 'catalogue-post':
                case 'all-catalogue-posts':
                case 'category-catalogue-posts':
                    return Post::resolveMenuItem($item, $url, $theme);
            }
        });
    }
}
