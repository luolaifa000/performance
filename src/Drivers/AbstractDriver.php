<?php

namespace Langyi\Performance\Drivers;

abstract class AbstractDriver implements Driver
{
    /**
     * 保存数据
     * 
     * @param array $xhprof_data
     */
    abstract public function save(array $xhprof_data = []): void;
    
    /**
     * 清理数据
     *
     * @param int $expire
     */
    abstract public function clean(int $expire): void;
    
    
    /**
     * Return request URI
     */
    public function getRequestUri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            return $_SERVER['REQUEST_URI'];
        }
    }
    
    /**
     * Return request URL
     */
    public function getRequestUrl()
    {
        $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        $addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null;
        $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        
        $scheme = $https ? 'https' : 'http';
        $host = $host ?: $addr;
        $port = (! $https && $port != 80 || $https && $port != 443) ? ":{$port}" : '';
        
        return "{$scheme}://{$host}{$port}{$uri}";
    }
    
    /**
     * 生成固定前缀的随机数
     * 
     * @return string
     */
    public function getUid(): string
    {
        return str_replace('.', '', uniqid('p_', true));
    }
    
    /**
     * JSONSQL
     * 
     * @return string
     */
    public function getDatabaseQueries(): string
    {
        return @json_encode(app('performance.eloquent')->getDatabaseQueries(),  \JSON_PARTIAL_OUTPUT_ON_ERROR);
    }
}


