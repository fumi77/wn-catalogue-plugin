<?php

namespace Winter\Catalogue;

use Backend;
use Backend\Models\UserRole;
use Event;
use System\Classes\PluginBase;
use Winter\Catalogue\Classes\TagProcessor;
use Winter\Catalogue\Models\Category;
use Winter\Catalogue\Models\Post;

class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'winter.catalogue::lang.plugin.name',
            'description' => 'winter.catalogue::lang.plugin.description',
            'author'      => 'Winter CMS',
            'icon'        => 'icon-pencil',
            'homepage'    => 'https://github.com/wintercms/wn-catalogue-plugin',
            'replaces'    => ['Smart.Catalogue' => '<= 1.7.0'],
        ];
    }

    /**
     * Registers the components provided by this plugin.
     */
    public function registerComponents(): array
    {
        return [
            \Winter\Catalogue\Components\Post::class       => 'cataloguePost',
            \Winter\Catalogue\Components\Posts::class      => 'cataloguePosts',
            \Winter\Catalogue\Components\Categories::class => 'catalogueCategories',
            \Winter\Catalogue\Components\RssFeed::class    => 'catalogueRssFeed'
        ];
    }

    /**
     * Registers the permissions provided by this plugin.
     */
    public function registerPermissions(): array
    {
        return [
            'winter.catalogue.manage_settings' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.manage_settings',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'winter.catalogue.access_posts' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.access_posts',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'winter.catalogue.access_categories' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.access_categories',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'winter.catalogue.access_other_posts' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.access_other_posts',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'winter.catalogue.access_import_export' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.access_import_export',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
            'winter.catalogue.access_publish' => [
                'tab'   => 'winter.catalogue::lang.catalogue.tab',
                'label' => 'winter.catalogue::lang.catalogue.access_publish',
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
                'label'       => 'winter.catalogue::lang.catalogue.menu_label',
                'url'         => Backend::url('winter/catalogue/posts'),
                'icon'        => 'icon-pencil',
                'iconSvg'     => 'plugins/winter/catalogue/assets/images/catalogue-icon.svg',
                'permissions' => ['winter.catalogue.*'],
                'order'       => 300,

                'sideMenu' => [
                    'new_post' => [
                        'label'       => 'winter.catalogue::lang.posts.new_post',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('winter/catalogue/posts/create'),
                        'permissions' => ['winter.catalogue.access_posts']
                    ],
                    'posts' => [
                        'label'       => 'winter.catalogue::lang.catalogue.posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('winter/catalogue/posts'),
                        'permissions' => ['winter.catalogue.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'winter.catalogue::lang.catalogue.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('winter/catalogue/categories'),
                        'permissions' => ['winter.catalogue.access_categories']
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
                'label' => 'winter.catalogue::lang.catalogue.menu_label',
                'description' => 'winter.catalogue::lang.catalogue.settings_description',
                'category' => 'winter.catalogue::lang.catalogue.menu_label',
                'icon' => 'icon-pencil',
                'class' => 'Winter\Catalogue\Models\Settings',
                'order' => 500,
                'keywords' => 'catalogue post category',
                'permissions' => ['winter.catalogue.manage_settings']
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
                'catalogue-category'       => 'winter.catalogue::lang.menuitem.catalogue_category',
                'all-catalogue-categories' => 'winter.catalogue::lang.menuitem.all_catalogue_categories',
                'catalogue-post'           => 'winter.catalogue::lang.menuitem.catalogue_post',
                'all-catalogue-posts'      => 'winter.catalogue::lang.menuitem.all_catalogue_posts',
                'category-catalogue-posts' => 'winter.catalogue::lang.menuitem.category_catalogue_posts',
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
