<?php

namespace ShibuyaKosuke\LaravelDbSchema\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Table
 * @package ShibuyaKosuke\LaravelDbSchema\Facades
 */
class Table extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'schema.table';
    }
}
