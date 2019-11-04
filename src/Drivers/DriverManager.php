<?php

namespace Langyi\Performance\Drivers;

use InvalidArgumentException;

class DriverManager 
{

    protected $app;

    protected $stores = [];


    public function __construct($app)
    {
        $this->app = $app;
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
        return $this->repository(new SqlStorage());
    }


    public function repository(Driver $store)
    {
        return new Repository($store);
    }


    public function getDefaultDriver()
    {
        return $this->app['config']['performance.storage'];
    }

    public function setDefaultDriver($name)
    {
        $this->app['config']['performance.storage'] = $name;
    }


    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
