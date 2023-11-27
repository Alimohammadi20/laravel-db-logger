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
        $this->publishConfig();
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
        $this->setRoute();
        $this->publishAssets();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dblogger');
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new CleanUpLogs)->dailyAt('00:00');
        });
    }

    protected function publishConfig(): void
    {
        $this->deleteDirectory(config_path('dblogger.php'));
        $this->publishes([
            __DIR__ . '/../config/dblogger.php' => config_path('dblogger.php'),
        ], 'dblogger::config');
    }

    protected function publishAssets(): void
    {
        $assetUrlPrefix = config('dblogger.asset_url');
        $this->deleteDirectory(public_path($assetUrlPrefix . '/vendor/alimi7372/dblogger'));
        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path($assetUrlPrefix . '/vendor/alimi7372/dblogger'),
        ], 'dblogger::public');
    }

    protected function setRoute(): void
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

    protected function deleteDirectory($dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}
