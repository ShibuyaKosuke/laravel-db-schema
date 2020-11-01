<?php

namespace ShibuyaKosuke\LaravelDbSchema\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface KeyColumnUsage
 * @package ShibuyaKosuke\LaravelDbSchema\Contracts
 *
 * @property-read string referenced_column_name
 * @property-read string referenced_table_name
 */
interface KeyColumnUsage
{
    /**
     * @return HasMany
     */
    public function referencedColumns(): HasMany;

    /**
     * @return HasMany
     */
    public function referencingColumns(): HasMany;
}
