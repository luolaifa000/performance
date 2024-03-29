<?php
namespace Langyi\Performance\Cores;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository;

/**
 * xhprof记录性能日志
 *
 * @author mancangluo
 *
 */
class PerformanceCore
{
    private $hit;
    
    private $enable;
    
    private $app;
    
    private $config;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->hit = $this->config->get('performance.hit');
        $this->enable = $this->config->get('performance.enable') && $this->isHit();
    }

    /**
     * 是否命中
     *
     * @param $hitProbability
     *
     * @return bool
     */
    private function isHit()
    {
        return mt_rand(1, $this->hit) == 1;
    }

    /**
     * 启动Xhprof性能检测服务
     *
     * @param int   $hitProbability 命中率，不能小于1
     * @param float $slowAppLog     慢日志阈值，单位s，不能小于0.1
     */
    public function start()
    {
        if ($this->enable && extension_loaded('xhprof')) {
            $filter = ['Illuminate', 'Symfony', 'Predis', 'Composer', 'Clockwork', 'Carbon', 'Monolog'];
            //启动性能收集
            xhprof_enable(XHPROF_FLAGS_NO_BUILTINS  | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY,
                [
                    'ignored_functions' => ['call_user_func','call_user_func_array'],
                    'laravel_namespace_prefix' => $filter
                ]);
            register_shutdown_function([$this, 'stop']);
        }
    }

    /**
     * 停止收集服务
     *
     * @param $slowAppLog
     */
    public function stop()
    {
        $xhprof_data = xhprof_disable();
        
        $this->app->make('performance.manager')->save($xhprof_data ?? []);
    }
    
    public function getEnable()
    {
        return $this->enable;
    }
    
    public function setEnable($enable)
    {
        $this->enable = $enable;
    }

}
