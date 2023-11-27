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
            __DIR__ . '/../config/dblogger.php' => config_path('dblogger.php'),
        ], 'dblogger::config');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        $this->setRoute();
        $this->loadAssets();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dblogger');
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new CleanUpLogs)->dailyAt('00:00');
        });
    }

    protected function loadAssets()
    {
        $assetUrlPrefix = config('dblogger.asset_url');
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path($assetUrlPrefix . '/vendor/alimi7372/dblogger'),
        ], 'dblogger::public');
    }
    protected function setRoute()
    {
        $routePrefix = config('dblogger.prefix');
        $routeMiddleware = config('dblogger.middleware');
        $routes = $this->app['router']->as('dblogger::');
        if ($routePrefix){
            $routes->prefix($routePrefix);
        }
        if ($routeMiddleware){
            $routes->middleware($routeMiddleware);
        }
        $routes->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/dblogger.php');
        });
    }
}
