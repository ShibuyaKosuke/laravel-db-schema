<?php

namespace ShibuyaKosuke\LaravelDbSchema\Test;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use ShibuyaKosuke\LaravelDbSchema\Facades\Column;
use ShibuyaKosuke\LaravelDbSchema\Facades\Table;
use ShibuyaKosuke\LaravelDbSchema\Providers\ServiceProvider;

/**
 * Class TestCase
 * @package ShibuyaKosuke\LaravelDbSchema\Test
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.connections', $app->config->get('database.connections'));
    }

    /**
     * @param Application $app
     */
    protected function useMySqlConnection(Application $app)
    {
        $app->config->set('database.default', 'mysql');
    }

    /**
     * @param Application $app
     */
    protected function usePostgreSqlConnection(Application $app): void
    {
        $app->config->set('database.default', 'pgsql');
    }

    /**
     * @param Application $app
     * @return array|string[]
     */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }

    /**
     * @param Application $app
     * @return string[]
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Table' => Table::class,
            'Column' => Column::class
        ];
    }
}
