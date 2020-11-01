<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ShibuyaKosuke\LaravelDbSchema\Contracts\KeyColumnUsage as KeyColumnUsageBase;

/**
 * Class KeyColumnUsage
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql
 */
class KeyColumnUsage extends Model implements KeyColumnUsageBase
{
    /**
     * @var string
     */
    protected $table = 'information_schema.key_column_usage';

    /**
     * @var string[]
     */
    protected $appends = [
        'referenced_table_name',
        'referenced_column_name'
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // add column_comment
        static::addGlobalScope('reference', function (Builder $builder) {
            $builder->select('information_schema.key_column_usage.*')
                ->addSelect(\DB::raw('constraint_column_usage.table_name as referenced_table_name'))
                ->addSelect(\DB::raw('constraint_column_usage.column_name as referenced_column_name'))
                ->leftJoin(
                    'information_schema.table_constraints',
                    'key_column_usage.constraint_name',
                    '=',
                    'table_constraints.constraint_name'
                )
                ->leftJoin(
                    'information_schema.constraint_column_usage',
                    'constraint_column_usage.constraint_name',
                    '=',
                    'table_constraints.constraint_name'
                )
                ->where('table_constraints.constraint_type', 'FOREIGN KEY');
        });
    }

    /**
     * @return HasMany
     */
    public function referencedColumns(): HasMany
    {
        return $this->hasMany(Column::class, 'column_name', 'referenced_column_name')
            ->where('information_schema.columns.table_name', $this->referenced_table_name)
            ->where('information_schema.columns.table_catalog', $this->table_catalog)
            ->where('information_schema.columns.table_schema', $this->table_schema);
    }

    /**
     * @return HasMany
     */
    public function referencingColumns(): HasMany
    {
        return $this->hasMany(Column::class, 'column_name', 'column_name')
            ->where('information_schema.columns.table_name', $this->referenced_table_name)
            ->where('information_schema.columns.table_catalog', $this->table_catalog)
            ->where('information_schema.columns.table_schema', $this->table_schema);
    }
}
