<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Mysql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TableScope
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Mysql
 */
class Scope implements \Illuminate\Database\Eloquent\Scope
{
    /**
     * @var string
     */
    private $database;

    /**
     * TableScope constructor.
     */
    public function __construct()
    {
        $config = app()->config;
        $default = $config->get('database.default');
        $this->database = $config->get("database.connections.$default.database");
    }

    /**
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder
            ->where('table_catalog', 'def')
            ->where('table_schema', $this->database)
            ->whereNotIn('table_name', ['migrations', 'failed_jobs', 'password_resets']);
    }
}
