<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Mysql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ShibuyaKosuke\LaravelDbSchema\Contracts\Column as ColumnContract;

/**
 * Class Column
 * @package ShibuyaKosuke\LaravelDbSchema\Databases
 */
class Column extends Database implements ColumnContract
{
    /**
     * @var string
     */
    protected $table = 'information_schema.columns';

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('columns', function (Builder $builder) {
            $builder->selectRaw('TABLE_CATALOG as table_catalog')
                ->selectRaw('TABLE_SCHEMA as table_schema')
                ->selectRaw('TABLE_NAME as table_name')
                ->selectRaw('COLUMN_NAME as column_name')
                ->selectRaw('ORDINAL_POSITION as ordinal_position')
                ->selectRaw('COLUMN_DEFAULT as column_default')
                ->selectRaw('IS_NULLABLE as is_nullable')
                ->selectRaw('DATA_TYPE as data_type')
                ->selectRaw('CHARACTER_MAXIMUM_LENGTH as character_maximum_length')
                ->selectRaw('CHARACTER_OCTET_LENGTH as character_octet_length')
                ->selectRaw('NUMERIC_PRECISION as numeric_precision')
                ->selectRaw('NUMERIC_SCALE as numeric_scale')
                ->selectRaw('DATETIME_PRECISION as datetime_precision')
                ->selectRaw('CHARACTER_SET_NAME as character_set_name')
                ->selectRaw('COLLATION_NAME as collation_name')
                ->selectRaw('COLUMN_TYPE as column_tye')
                ->selectRaw('COLUMN_KEY as column_key')
                ->selectRaw('EXTRA as extra')
                ->selectRaw('PRIVILEGES as privileges')
                ->selectRaw('COLUMN_COMMENT as column_comment')
                ->selectRaw('GENERATION_EXPRESSION as generation_expression');
        });
    }

    /**
     * @return boolean
     */
    public function isPrimary(): bool
    {
        return $this->column_key === 'PRI';
    }

    /**
     * @return boolean
     */
    public function isUnique(): bool
    {
        return $this->column_key === 'UNI';
    }

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_name', 'table_name');
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|ColumnContract|null
     */
    public function belongsToColumn()
    {
        $keyColumnUsage = KeyColumnUsage::query()
            ->where('column_name', $this->getAttribute('column_name'))
            ->where('table_name', $this->getAttribute('table_name'))
            ->first();

        if (
            $keyColumnUsage &&
            $keyColumnUsage->referenced_table_name &&
            $relation_name = Str::singular($keyColumnUsage->referenced_table_name)
        ) {

            /** @var Collection $column */
            return $this->getColumn($keyColumnUsage->referenced_table_name, $keyColumnUsage->referenced_column_name);
        }

        return null;
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|ColumnContract|null
     */
    public function belongsToManyColumn()
    {
        $default = config('database.default');

        $column = $this->getAttribute('column_name');
        $table = $this->getAttribute('table_name');
        $database = config("database.connections.$default.database");

        $keyColumnUsage = KeyColumnUsage::query()
            ->whereIn(
                \DB::raw('(table_catalog, table_schema, table_name)'),
                function ($query) use ($database, $column, $table) {
                    $query->select('table_catalog')
                        ->addSelect('table_schema')
                        ->addSelect('table_name')
                        ->from('information_schema.key_column_usage')
                        ->where('table_catalog', 'def')
                        ->where('referenced_column_name', $column)
                        ->where('referenced_table_name', $table)
                        ->where('table_schema', $database)
                        ->whereNotIn('table_name', ['migrations', 'failed_jobs', 'password_resets']);
                }
            )
            ->whereNotNull('referenced_column_name')
            ->whereNotNull('referenced_table_name')
            ->where('referenced_table_name', '!=', $table)
            ->first();

        if ($keyColumnUsage && $relation_name = Str::singular($keyColumnUsage->referenced_table_name)) {
            return $this->getColumn($keyColumnUsage->referenced_table_name, $keyColumnUsage->referenced_column_name);
        }

        return null;
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|ColumnContract|null
     */
    public function hasManyColumns()
    {
        $keyColumnUsage = KeyColumnUsage::query()
            ->where('referenced_column_name', $this->getAttribute('column_name'))
            ->where('referenced_table_name', $this->getAttribute('table_name'))
            ->first();

        if ($keyColumnUsage && $relation_name = $keyColumnUsage->table_name) {
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
