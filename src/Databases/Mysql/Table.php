<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Mysql;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use ShibuyaKosuke\LaravelDbSchema\Contracts\Table as TableContract;

/**
 * Class Table
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Mysql
 *
 * @property-read string engine
 * @property-read string version
 * @property-read string row_format
 * @property-read string table_rows
 * @property-read string avg_row_length
 * @property-read string data_length
 * @property-read string max_data_length
 * @property-read string index_length
 * @property-read string data_free
 * @property-read string auto_increment
 * @property-read string create_time
 * @property-read string update_time
 * @property-read string check_time
 * @property-read string table_collation
 * @property-read string checksum
 * @property-read string create_options
 * @property-read string table_comment
 */
class Table extends Database implements TableContract
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

        static::addGlobalScope('columns', function (Builder $builder) {
            $builder->selectRaw('TABLE_CATALOG as table_catalog')
                ->selectRaw('TABLE_SCHEMA as table_schema')
                ->selectRaw('TABLE_NAME as table_name')
                ->selectRaw('TABLE_TYPE as table_type')
                ->selectRaw('ENGINE as table_type')
                ->selectRaw('VERSION as table_type')
                ->selectRaw('ROW_FORMAT as table_type')
                ->selectRaw('TABLE_ROWS as table_rows')
                ->selectRaw('AVG_ROW_LENGTH as avg_row_length')
                ->selectRaw('DATA_LENGTH as data_length')
                ->selectRaw('MAX_DATA_LENGTH as max_data_length')
                ->selectRaw('INDEX_LENGTH as index_length')
                ->selectRaw('DATA_FREE as fata_free')
                ->selectRaw('AUTO_INCREMENT as auto_increment')
                ->selectRaw('CREATE_TIME as create_time')
                ->selectRaw('UPDATE_TIME as update_time')
                ->selectRaw('CHECK_TIME as check_time')
                ->selectRaw('TABLE_COLLATION as table_collation')
                ->selectRaw('CHECKSUM as checksum')
                ->selectRaw('CREATE_OPTIONS as create_options')
                ->selectRaw('TABLE_COMMENT as table_comment')
                ->where('TABLE_COMMENT', '!=', '');
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
     * @return array
     */
    public function getPrimaryKeyColumn(): array
    {
        /** @var Collection $columns */
        return $this->columns->filter(function (Column $column) {
            return $column->column_key === 'PRI';
        })->pluck('COLUMN_NAME')->toArray();
    }

    /**
     * @return HasMany
     */
    public function columns(): HasMany
    {
        return $this->hasMany(Column::class, 'table_name', 'table_name')
            ->orderBy('ordinal_position');
    }
}
