<?php

namespace ShibuyaKosuke\LaravelDbSchema\Test;

use Exception;
use ShibuyaKosuke\LaravelDbSchema\Contracts\Table as TableContract;
use ShibuyaKosuke\LaravelDbSchema\Table;

/**
 * Class TableTest
 * @package ShibuyaKosuke\LaravelDbSchema\Test
 */
class TableTest extends TestCase
{
    /**
     * @var Table
     */
    private $table;

    private $connections = [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'homestead',
            'username' => 'homestead',
            'password' => 'secret'
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => 5432,
            'database' => 'homestead',
            'username' => 'homestead',
            'password' => 'secret'
        ]
    ];

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->config->set('database.connections', $this->connections);

        $this->getEnvironmentSetUp($this->app);
//        $this->useMySqlConnection($this->app);
        $this->usePostgreSqlConnection($this->app);

        $this->table = new Table($this->app['config']);
    }

//    /**
//     * @return void
//     */
//    public function testExample(): void
//    {
//        /** @var Column[]|null $primary_keys */
//        $primary_keys = $this->table->get()->map(function (TableContract $table) {
//            return $table->getPrimaryKeyColumn();
//        });
////        dump($primary_keys);
//    }

//    /**
//     * @return void
//     */
//    public function testColumnAttributes(): void
//    {
//        $this->table->with(['columns'])->get()->each(function (TableContract $table) {
//            $table->columns->each(function (Column $column) {
//                dump($column->column_comment);
//            });
//        });
//    }

    public function testTables()
    {
        $this->table->get()->each(function (TableContract $table) {
            dump($table->table_comment);
        });
    }
}
