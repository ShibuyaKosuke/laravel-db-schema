<?php

namespace ShibuyaKosuke\LaravelDbSchema\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Column
 * @package ShibuyaKosuke\LaravelDbSchema\Facades
 */
class Column extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'schema.column';
    }
}
