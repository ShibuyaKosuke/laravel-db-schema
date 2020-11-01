<?php

namespace ShibuyaKosuke\LaravelDbSchema;

use Exception;
use Illuminate\Config\Repository;
use ShibuyaKosuke\LaravelDbSchema\Databases\Mysql\Column as MysqlColumn;
use ShibuyaKosuke\LaravelDbSchema\Databases\Mysql\Table as MysqlTable;
use ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql\Column as PostgresqlColumn;
use ShibuyaKosuke\LaravelDbSchema\Databases\Postgresql\Table as PostgresqlTable;

/**
 * Class Database
 * @package ShibuyaKosuke\LaravelDbSchema
 */
abstract class Database
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Contracts\Table|Contracts\Column
     */
    protected $object;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->object, $name], $arguments);
    }

    /**
     * Get default database.
     * @return string
     */
    protected function getDefaultDatabase(): string
    {
        return $this->config->get('database.default');
    }

    /**
     * @param string $className
     * @return void
     * @throws Exception
     */
    protected function setObject($className): void
    {
        switch ($className) {
            case Table::class:
                $this->setTableModel();
                break;
            case Column::class:
                $this->setColumnModel();
                break;
        }
    }

    /**
     * Set object.
     * @return void
     * @throws Exception
     */
    private function setTableModel(): void
    {
        $default = $this->getDefaultDatabase();

        switch ($default) {
            case 'mysql':
                $this->object = new MysqlTable();
                break;
            case 'pgsql':
                $this->object = new PostgresqlTable();
                break;
            default:
                throw new Exception(sprintf('Database %s is not supported.', $default));
        }
    }

    /**
     * Set object.
     * @return void
     * @throws Exception
     */
    private function setColumnModel(): void
    {
        $default = $this->getDefaultDatabase();

        switch ($default) {
            case 'mysql':
                $this->object = new MysqlColumn();
                break;
            case 'pgsql':
                $this->object = new PostgresqlColumn();
                break;
            default:
                throw new Exception(sprintf('Database %s is not supported.', $default));
        }
    }
}
