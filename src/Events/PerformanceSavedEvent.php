<?php
namespace Langyi\Performance\Events;

class PerformanceSavedEvent
{
    public $data;
    
    public $url;
    
    public function __construct(array $data = [], string $url) 
    {
        $this->data = $data; 
        $this->url = $url;
    }
}
