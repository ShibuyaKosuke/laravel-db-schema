<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Mysql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ShibuyaKosuke\LaravelDbSchema\Contracts\KeyColumnUsage as KeyColumnUsageBase;

/**
 * Class KeyColumnUsage
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Mysql
 *
 * @property-read string referenced_column_name
 * @property-read string referenced_table_name
 */
class KeyColumnUsage extends Database implements KeyColumnUsageBase
{
    /**
     * @var string
     */
    protected $table = 'information_schema.key_column_usage';

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('columns', function (Builder $builder) {
            $builder->selectRaw('CONSTRAINT_CATALOG as constraint_catalog')
                ->selectRaw('CONSTRAINT_SCHEMA as constraint_schema')
                ->selectRaw('CONSTRAINT_NAME as constraint_name')
                ->selectRaw('TABLE_CATALOG as table_catalog')
                ->selectRaw('TABLE_SCHEMA as table_schema')
                ->selectRaw('TABLE_NAME as table_name')
                ->selectRaw('COLUMN_NAME as column_name')
                ->selectRaw('ORDINAL_POSITION as ordinal_position')
                ->selectRaw('POSITION_IN_UNIQUE_CONSTRAINT as position_in_unique_constraint')
                ->selectRaw('REFERENCED_TABLE_SCHEMA as referenced_table_schema')
                ->selectRaw('REFERENCED_TABLE_NAME as referenced_table_name')
                ->selectRaw('REFERENCED_COLUMN_NAME as referenced_column_name');
        });
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * @return HasMany
     */
    public function referencedColumns(): HasMany
    {
        // TODO: Implement referencingColumns() method.
        return $this->hasMany(Column::class);
    }

    /**
     * @return HasMany
     */
    public function referencingColumns(): HasMany
    {
        // TODO: Implement referencingColumns() method.
        return $this->hasMany(Column::class);
    }
}
