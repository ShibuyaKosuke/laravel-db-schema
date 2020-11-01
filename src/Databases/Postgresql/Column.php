<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use ShibuyaKosuke\LaravelDbSchema\Contracts\Column as ColumnContract;

/**
 * Class Column
 * @package ShibuyaKosuke\LaravelDbSchema\Databases
 *
 * @property-read string column_comment
 */
class Column extends Model implements ColumnContract
{
    /**
     * @var string
     */
    protected $table = 'information_schema.columns';

    /**
     * @var string[]
     */
    protected $appends = [
        'column_comment',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new ColumnScope());

        // add column_comment
        static::addGlobalScope('comment', function (Builder $builder) {
            $builder->select('information_schema.columns.*')
                ->addSelect([
                    DB::raw('pg_description.description as column_comment'),
                    DB::raw('table_constraints.constraint_type as constraint_type')
                ])
                ->leftJoin(
                    'pg_stat_user_tables',
                    'information_schema.columns.table_name',
                    '=',
                    'pg_stat_user_tables.relname'
                )
                ->leftJoin('pg_description', function ($join) {
                    $join->on('pg_stat_user_tables.relid', '=', 'pg_description.objoid')
                        ->on('pg_description.objsubid', '=', 'information_schema.columns.ordinal_position');
                })
                ->leftJoin('information_schema.key_column_usage', function ($join) {
                    $join->on('columns.column_name', '=', 'key_column_usage.column_name')
                        ->on('columns.table_name', '=', 'key_column_usage.table_name')
                        ->on('columns.table_catalog', '=', 'key_column_usage.table_catalog')
                        ->on('columns.table_schema', '=', 'key_column_usage.table_schema');
                })
                ->leftJoin('information_schema.constraint_column_usage', function ($join) {
                    $join->on('columns.table_name', '=', 'constraint_column_usage.table_name')
                        ->on('key_column_usage.constraint_name', '=', 'constraint_column_usage.constraint_name');
                })
                ->leftJoin('information_schema.table_constraints', function ($join) {
                    $join->on('key_column_usage.constraint_catalog', '=', 'table_constraints.constraint_catalog')
                        ->on('key_column_usage.constraint_schema', '=', 'table_constraints.constraint_schema')
                        ->on('key_column_usage.constraint_name', '=', 'table_constraints.constraint_name');
                })
                ->whereNotNull('pg_description.description');
        });
    }

    /**
     * @return boolean
     */
    public function isPrimary(): bool
    {
        return $this->constraint_type === 'PRIMARY KEY';
    }

    /**
     * @return boolean
     */
    public function isUnique(): bool
    {
        return $this->constraint_type === 'UNIQUE';
    }

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_name', 'table_name');
    }

    /**
     * @return Builder|Model|object|ColumnContract|null
     */
    public function belongsToColumn()
    {
        $keyColumnUsage = KeyColumnUsage::query()
            ->where('key_column_usage.column_name', $this->column_name)
            ->where('key_column_usage.table_name', $this->table_name)
            ->first();

        if ($keyColumnUsage) {
            return $this->getColumn($keyColumnUsage->referenced_table_name, $keyColumnUsage->referenced_column_name);
        }

        return null;
    }

    /**
     * @return ColumnContract|void
     */
    public function belongsToManyColumn()
    {
        $default = config('database.default');

        $column = $this->getAttribute('column_name');
        $table = $this->getAttribute('table_name');
        $database = config("database.connections.$default.database");

        $keyColumnUsage = KeyColumnUsage::query()
            ->whereIn(
                DB::raw('(key_column_usage.table_catalog, key_column_usage.table_schema, key_column_usage.table_name)'),
                function ($query) use ($database, $column, $table) {
                    $query->select('information_schema.key_column_usage.table_catalog')
                        ->addSelect('information_schema.key_column_usage.table_schema')
                        ->addSelect('information_schema.key_column_usage.table_name')
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
                        ->where('table_constraints.constraint_type', 'FOREIGN KEY')
                        ->from('information_schema.key_column_usage')
                        ->where('constraint_column_usage.column_name', $column)
                        ->where('constraint_column_usage.table_name', $table)
                        ->where('information_schema.key_column_usage.table_catalog', $database)
                        ->whereNotIn(
                            'information_schema.key_column_usage.table_name',
                            ['migrations', 'failed_jobs', 'password_resets']
                        );
                }
            )
            ->whereNotNull('constraint_column_usage.column_name')
            ->whereNotNull('constraint_column_usage.table_name')
            ->where('constraint_column_usage.table_name', '!=', $table)
            ->first();

        if ($keyColumnUsage && $relation_name = \Str::singular($keyColumnUsage->referenced_table_name)) {
            return $this->getColumn($keyColumnUsage->referenced_table_name, $keyColumnUsage->referenced_column_name);
        }

        return null;
    }

    /**
     * @return Builder|Model|object|ColumnContract|null
     */
    public function hasManyColumns()
    {
        $keyColumnUsage = KeyColumnUsage::query()
            ->where('constraint_column_usage.column_name', $this->column_name)
            ->where('constraint_column_usage.table_name', $this->table_name)
            ->first();

        if ($keyColumnUsage) {
            return $this->getColumn($keyColumnUsage->table_name, $keyColumnUsage->column_name);
        }

        return null;
    }

    /**
     * @param string $table
     * @param string $column
     * @return Builder|Model|object|ColumnContract|null
     */
    private function getColumn(string $table, string $column)
    {
        return self::query()
            ->where('information_schema.columns.table_name', $table)
            ->where('information_schema.columns.column_name', $column)
            ->first();
    }
}
