<?php
namespace Langyi\Performance\Drivers;

class FileStorage extends AbstractDriver
{
    
    public function save(array $xhprof_data = []): void
    {
        return;
    }
    
    public function clean(int $expire): void
    {
        return;   
    }
    
}
