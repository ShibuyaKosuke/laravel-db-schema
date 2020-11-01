<?php

namespace ShibuyaKosuke\LaravelDbSchema;

use Exception;
use Illuminate\Config\Repository;

/**
 * Class Column
 * @package ShibuyaKosuke\LaravelDbSchema
 */
class Column extends Database
{
    /**
     * Table constructor.
     * @param Repository $config
     * @throws Exception
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->setObject(__CLASS__);
    }
}
