<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ShibuyaKosuke\LaravelDbSchema\Contracts\Table as TableContract;

/**
 * Class Table
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql
 *
 * @property-read HasMany|ConstraintColumnUsage[]|null constraintColumnUsages
 *
 * @property-read string self_referencing_column_name
 * @property-read string reference_generation
 * @property-read string user_defined_type_catalog
 * @property-read string user_defined_type_schema
 * @property-read string user_defined_type_name
 * @property-read string is_insertable_into
 * @property-read string is_typed
 * @property-read string commit_action
 * @property-read string table_comment
 */
class Table extends Model implements TableContract
{
    /**
     * @var string
     */
    protected $table = 'information_schema.tables';

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new TableScope());

        // add column_comment
        static::addGlobalScope('comment', function (Builder $builder) {
            $builder->select('information_schema.tables.*')
                ->addSelect(\DB::raw('pg_description.description as table_comment'))
                ->leftJoin(
                    'pg_stat_user_tables',
                    'information_schema.tables.table_name',
                    '=',
                    'pg_stat_user_tables.relname'
                )
                ->leftJoin('pg_description', function ($join) {
                    $join->on('pg_stat_user_tables.relid', '=', 'pg_description.objoid')
                        ->where('pg_description.objsubid', '=', 0);
                })
                ->whereNotNull('pg_description.description');
        });
    }

    /**
     * @return array
     */
    public function getPrimaryKeyColumn(): array
    {
        return $this->constraintColumnUsages->filter(function (ConstraintColumnUsage $constraintColumnUsage) {
            return Str::endsWith($constraintColumnUsage->constraint_name, '_pkey');
        })->pluck('column_name')->toArray();
    }

    /**
     * @return HasMany
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class, 'table_name', 'table_name')
            ->where('information_schema.columns.table_schema', $this->table_schema)
            ->where('information_schema.columns.table_catalog', $this->table_catalog)
            ->orderBy('information_schema.columns.ordinal_position');
    }

    /**
     * @return HasMany
     */
    public function constraintColumnUsages(): HasMany
    {
        return $this->hasMany(ConstraintColumnUsage::class, 'table_name', 'table_name')
            ->whereColumn('table_schema', 'table_schema')
            ->whereColumn('table_catalog', 'table_catalog');
    }
}
