<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TableScope
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql
 */
class TableScope implements \Illuminate\Database\Eloquent\Scope
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
        $builder->where('information_schema.tables.table_schema', 'public')
            ->where('information_schema.tables.table_catalog', $this->database)
            ->whereNotIn('information_schema.tables.table_name', ['migrations', 'failed_jobs', 'password_resets']);
    }
}
