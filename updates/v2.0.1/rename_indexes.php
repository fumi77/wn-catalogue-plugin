<?php namespace Smart\Catalogue\Updates;

use Db;
use Schema;
use Winter\Storm\Database\Updates\Migration;

class RenameIndexes extends Migration
{
    const TABLES = [
        'categories',
        'posts',
        'posts_categories'
    ];

    public function up()
    {
        foreach (self::TABLES as $table) {
            $from = 'rainlab_catalogue_' . $table;
            $to = 'smart_catalogue_' . $table;

            $this->updateIndexNames($from, $to, $to);
        }
    }

    public function down()
    {
        foreach (self::TABLES as $table) {
            $from = 'smart_catalogue_' . $table;
            $to = 'rainlab_catalogue_' . $table;

            $this->updateIndexNames($from, $to, $from);
        }
    }

    public function updateIndexNames($from, $to, $table)
    {
        Schema::table($table, function ($blueprint) use ($from, $to) {
            foreach ($this->getIndexes($blueprint) as $index) {
                if (is_object($index) ? $index->isPrimary() : $index['primary']) {
                    continue;
                }

                $old = is_object($index) ? $index->getName() : $index['name'];
                $new = str_replace($from, $to, $old);

                $blueprint->renameIndex($old, $new);
            }
        });
    }

    public function getIndexes($blueprint)
    {
        $connection = Schema::getConnection();
        $table = $blueprint->getTable();

        if (method_exists($connection, 'getDoctrineSchemaManager')) {
            $sm = $connection->getDoctrineSchemaManager();
            return $sm->listTableDetails($table)->getIndexes();
        } else {
            return $connection->getSchemaBuilder()->getIndexes($table);
        }
    }
}
