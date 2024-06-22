<?php namespace Winter\Catalogue\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;
use Winter\Catalogue\Models\Category as CategoryModel;

class PostsAddMetadata extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('smart_catalogue_posts', 'metadata')) {
            return;
        }

        Schema::table('smart_catalogue_posts', function($table)
        {
            $table->mediumText('metadata')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('smart_catalogue_posts', 'metadata')) {
            Schema::table('smart_catalogue_posts', function ($table) {
                $table->dropColumn('metadata');
            });
        }
    }

}
