<?php

namespace Langyi\Performance;

use Langyi\Performance\Cores\Performance;
use Illuminate\Support\ServiceProvider;
use Langyi\Performance\DataSource\EloquentDataSource;
use Langyi\Performance\Drivers\DriverManager;
use Langyi\Performance\Cores\PerformanceCore;
use Illuminate\Console\Scheduling\Schedule;
use Langyi\Performance\Commands\PerformanceCleanCommand;



class PerformanceServiceProvider extends ServiceProvider
{    
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigFile() => config_path('performance.php'),
            ], 'config');
        }
        
        if ($this->app->make('performance')->getEnable()) {
            $this->app['performance.eloquent']->listenToEvents();
        }
        
        $this->app->booted(function() {
            $this->app->make(Schedule::class)->command(PerformanceCleanCommand::class, [
                '--expire' => $this->app->make('config')->get('performance.expire')
            ])->daily()
            ->withoutOverlapping();
        });
        
        
        
    }
    

    public function register()
    {
        
        $this->app->singleton('performance', function ($app) {
            return new PerformanceCore($app, $app['config']);
        });
        $this->app->singleton('performance.manager', function ($app) {
            return new DriverManager($app, $app['config']);
        });

        $this->loadMigrationsFrom($this->getMigrateFile());
        
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'performance'
        );
        
        $this->app->singleton('performance.eloquent', function ($app) {
            return (new EloquentDataSource($app['db'], $app['events']))
            ->collectStackTraces(false);
        });
        $this->commands([
            PerformanceCleanCommand::class
        ]);

    }
    
    private function getMigrateFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src/Migrations';
    }

    private function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'performance.php';
    }
}
