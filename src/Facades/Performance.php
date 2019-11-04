<?php

namespace Langyi\Performance\Facades;
use Illuminate\Support\Facades\Facade;

class Performance extends Facade
{
    
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'performance';
    }
}