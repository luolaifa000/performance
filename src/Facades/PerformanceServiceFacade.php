<?php

namespace Langyi\Performance\Facades;

use Langyi\Performance\Cores\Performance;
use Illuminate\Support\ServiceProvider;
use Langyi\Performance\DataSource\EloquentDataSource;
use Langyi\Performance\Drivers\DriverManager;



class PerformanceServiceFacade extends ServiceProvider
{    
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigFile() => config_path('performance.php'),
            ], 'config');
        }
        
        if ($this->app->make(Performance::class)->getEnable()) {
            
            $this->app['performance.eloquent']->listenToEvents();
        }
    }
    

    public function register()
    {
        $this->app->singleton(Performance::class, function ($app) {
            return new Performance($app);
        });
        $this->app->singleton('performance_manager', function ($app) {
            return new DriverManager($app);
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

    }
    
    private function getMigrateFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Migrations';
    }

    private function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'performance.php';
    }
}
