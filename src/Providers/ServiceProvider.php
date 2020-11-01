<?php

namespace ShibuyaKosuke\LaravelDbSchema\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as ServiceProviderBase;
use ShibuyaKosuke\LaravelDbSchema\Column;
use ShibuyaKosuke\LaravelDbSchema\Table;

/**
 * Class ServiceProvider
 * @package ShibuyaKosuke\LaravelDbSchema\Providers
 */
class ServiceProvider extends ServiceProviderBase
{
    /**
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('schema.table', function (Application $app) {
            return new Table($app['config']);
        });

        $this->app->singleton('schema.column', function (Application $app) {
            return new Column($app['config']);
        });
    }
}
