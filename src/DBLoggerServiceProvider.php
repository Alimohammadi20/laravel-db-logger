<?php

namespace Alimi7372\DBLogger;

use Alimi7372\DBLogger\Jobs\CleanUpLogs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class DBLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::register();
        $this->publishes([
            __DIR__ . '/../config/logger.php' => config_path('logger.php'),
        ],'logger');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        $this->loadAssets();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'logger');
        $this->setRoute();
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new CleanUpLogs)->dailyAt('00:00');
        });

    }

    protected function loadAssets()
    {
        $assetUrlPrefix = config('logger.asset_url');
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path($assetUrlPrefix . '/vendor/alimi7372/logger'),
        ], 'public');
    }
    protected function setRoute()
    {
        $routePrefix = config('logger.prefix');
        $routeMiddleware = config('logger.middleware');
        $routes = $this->app['router']->as('log::');
        if ($routePrefix){
            $routes->prefix($routePrefix);
        }
        if ($routeMiddleware){
            $routes->middleware($routeMiddleware);
        }
        $routes->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/logger.php');
        });
    }
}
