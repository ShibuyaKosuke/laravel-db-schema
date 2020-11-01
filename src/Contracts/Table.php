<?php

namespace ShibuyaKosuke\LaravelDbSchema\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Interface Table
 * @package ShibuyaKosuke\LaravelDbSchema\Contracts
 *
 * @property-read HasMany|Collection|Column[] columns
 *
 * @property-read string table_catalog
 * @property-read string table_schema
 * @property-read string table_name
 * @property-read string table_type
 * @property-read string table_comment
 */
interface Table
{
    /**
     * @return array
     */
    public function getPrimaryKeyColumn(): array;

    /**
     * @return HasMany|Column[]
     */
    public function columns(): HasMany;
}
