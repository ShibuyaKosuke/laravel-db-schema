<?php

namespace ShibuyaKosuke\LaravelDbSchema\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Interface Column
 * @package ShibuyaKosuke\LaravelDbSchema\Contracts
 *
 * @property-read string table_catalog
 * @property-read string table_schema
 * @property-read string table_name
 * @property-read string column_name
 * @property-read int ordinal_position
 * @property-read string column_default
 * @property-read string is_nullable
 * @property-read string data_type
 * @property-read string character_maximum_length
 * @property-read string character_octet_length
 * @property-read int numeric_precision
 * @property-read int numeric_precision_radix
 * @property-read int numeric_scale
 * @property-read string datetime_precision
 * @property-read string character_set_name
 * @property-read string collation_name
 * @property-read string generation_expression
 * @property-read string column_comment
 *
 * @property-read string column_type mysql only
 * @property-read string column_key mysql only
 * @property-read string extra mysql only
 * @property-read string privileges mysql only
 * @property-read string srs_id mysql only
 *
 * @property-read string interval_type postgresql only
 * @property-read string interval_precision postgresql only
 * @property-read string character_set_catalog postgresql only
 * @property-read string character_set_schema postgresql only
 * @property-read string collation_catalog postgresql only
 * @property-read string collation_schema postgresql only
 * @property-read string domain_catalog postgresql only
 * @property-read string domain_schema postgresql only
 * @property-read string domain_name postgresql only
 * @property-read string udt_catalog postgresql only
 * @property-read string udt_schema postgresql only
 * @property-read string udt_name postgresql only
 * @property-read string scope_catalog postgresql only
 * @property-read string scope_schema postgresql only
 * @property-read string scope_name postgresql only
 * @property-read string maximum_cardinality postgresql only
 * @property-read int dtd_identifier postgresql only
 * @property-read string is_self_referencing postgresql only
 * @property-read string is_identity postgresql only
 * @property-read string identity_generation postgresql only
 * @property-read string identity_start postgresql only
 * @property-read string identity_increment postgresql only
 * @property-read string identity_maximum postgresql only
 * @property-read string identity_minimum postgresql only
 * @property-read string identity_cycle postgresql only
 * @property-read string is_generated postgresql only
 * @property-read string is_updatable postgresql only
 */
interface Column
{
    /**
     * @return boolean
     */
    public function isPrimary(): bool;

    /**
     * @return boolean
     */
    public function isUnique(): bool;

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo;

    /**
     * @return Column
     */
    public function belongsToColumn();

    /**
     * @return Column
     */
    public function belongsToManyColumn();

    /**
     * @return Column
     */
    public function hasManyColumns();
}
