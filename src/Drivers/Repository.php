<?php

namespace Langyi\Performance\Drivers;


class Repository 
{
    protected $store;

    public function __construct(Driver $store)
    {
        $this->store = $store;
    }


    
    public function __call($method, $parameters)
    {
        
        return $this->store->$method(...$parameters);
    }


    public function __clone()
    {
        $this->store = clone $this->store;
    }
}
