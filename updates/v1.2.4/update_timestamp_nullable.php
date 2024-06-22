<?php namespace Winter\Catalogue\Updates;

use Winter\Storm\Database\Updates\Migration;
use DbDongle;

class UpdateTimestampsNullable extends Migration
{
    public function up()
    {
        DbDongle::disableStrictMode();

        DbDongle::convertTimestamps('smart_catalogue_posts');
        DbDongle::convertTimestamps('smart_catalogue_categories');
    }

    public function down()
    {
        // ...
    }
}
