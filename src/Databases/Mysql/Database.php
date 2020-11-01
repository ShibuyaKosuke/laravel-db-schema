<?php

namespace ShibuyaKosuke\LaravelDbSchema\Databases\Mysql;

use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    /**
     * @var Scope
     */
    protected static $scope;

    /**
     * Database constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new Scope());
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute(strtoupper($key));
    }
}
