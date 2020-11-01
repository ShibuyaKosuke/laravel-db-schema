<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConstraintColumnUsage
 * @package ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql
 *
 * @property-read string table_catalog
 * @property-read string table_schema
 * @property-read string table_name
 * @property-read string column_name
 * @property-read string constraint_catalog
 * @property-read string constraint_schema
 * @property-read string constraint_name
 */
class ConstraintColumnUsage extends Model
{
    /**
     * @var string
     */
    protected $table = 'information_schema.constraint_column_usage';
}
