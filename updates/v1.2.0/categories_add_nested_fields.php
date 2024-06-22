<?php namespace Winter\Catalogue\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;
use Winter\Catalogue\Models\Category;

class CategoriesAddNestedFields extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('smart_catalogue_categories', 'parent_id')) {
            return;
        }

        Schema::table('smart_catalogue_categories', function($table)
        {
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
        });

        Category::extend(function ($model) {
            $model->setTable('smart_catalogue_categories');
        });

        foreach (Category::all() as $category) {
            $category->setDefaultLeftAndRight();
            $category->save();
        }

        Category::extend(function ($model) {
            $model->setTable('smart_catalogue_categories');
        });
    }

    public function down()
    {
    }
}
