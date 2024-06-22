<?php namespace Smart\Catalogue\Updates;

use Carbon\Carbon;
use Smart\Catalogue\Models\Post;
use Smart\Catalogue\Models\Category;
use Winter\Storm\Database\Updates\Seeder;

class SeedAllTables extends Seeder
{

    public function run()
    {
        Post::extend(function ($model) {
            $model->setTable('smart_catalogue_posts');
        });

        Post::create([
            'title' => 'First catalogue post',
            'slug' => 'first-catalogue-post',
            'content' => '
This is your first ever **catalogue post**! It might be a good idea to update this post with some more relevant content.

You can edit this content by selecting **Catalogue** from the administration back-end menu.

*Enjoy the good times!*
            ',
            'excerpt' => 'The first ever catalogue post is here. It might be a good idea to update this post with some more relevant content.',
            'published_at' => Carbon::now(),
            'published' => true
        ]);

        Category::extend(function ($model) {
            $model->setTable('smart_catalogue_categories');
        });

        Category::create([
            'name' => trans('smart.catalogue::lang.categories.uncategorized'),
            'slug' => 'uncategorized',
        ]);

        Post::extend(function ($model) {
            $model->setTable('smart_catalogue_posts');
        });

        Category::extend(function ($model) {
            $model->setTable('smart_catalogue_categories');
        });
    }
}
