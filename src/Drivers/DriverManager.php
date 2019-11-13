<?php

namespace Langyi\Performance\Drivers;

use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as RepositoryConfig;

class DriverManager 
{

    private $app;
    
    private $config;

    protected $stores = [];


    public function __construct(Application $app, RepositoryConfig $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function store($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        
        return $this->stores[$name] = $this->get($name);
    }

    protected function get($name)
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    protected function resolve($name)
    {
        $driverMethod = 'create'.ucfirst($name).'Driver';
        
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}();
        } else {
            throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }


    protected function createFileDriver()
    {
        return $this->repository(new FileStorage());
    }

    protected function createSqlDriver()
    {
        return $this->repository(new SqlStorage($this->app['events']));
    }


    public function repository(Driver $store): Repository
    {
        return new Repository($store);
    }


    public function getDefaultDriver(): string
    {
        return $this->config->get('performance.storage');
    }

    public function setDefaultDriver($name)
    {
        $this->config->set('performance.storage', $name);
    }


    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
