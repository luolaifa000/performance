<?php
namespace Langyi\Performance\Drivers;

use Langyi\Performance\Models\Performance as PModel;
use Illuminate\Support\Facades\Event;
use Langyi\Performance\Events\PerformanceSavedEvent;
use Langyi\Performance\Exceptions\PerformanceException;
use Illuminate\Contracts\Events\Dispatcher;

class SqlStorage extends AbstractDriver
{
    private $event;
    
    public function __construct(Dispatcher $event)
    {
        $this->event = $event;
    }
    
    /**
     * 保存性能数据到DB
     * 
     * {@inheritDoc}
     * @see \Langyi\Performance\Drivers\AbstractDriver::save()
     */
    public function save(array $xhprof_data = []): void
    {
        
        if (!isset($xhprof_data[SELF::MAIN_SYMBOL])) {
            return ;
        }
        $main = $xhprof_data[SELF::MAIN_SYMBOL];
        $xhprof_data = serialize($xhprof_data);
        $model = new PModel();
        $model->url = $this->getRequestUrl();
        $model->uri = explode('?', $this->getRequestUri())[0];
        $model->uid = $this->getUid();
        $model->content = $xhprof_data;
        $model->ct = isset($main[self::CT_SYMBOL]) ? $main[self::CT_SYMBOL] : 0;
        $model->wt = isset($main[self::WT_SYMBOL]) ? $main[self::WT_SYMBOL] : 0;
        $model->cpu = isset($main[self::CPU_SYMBOL]) ? $main[self::CPU_SYMBOL] : 0;
        $model->mu = isset($main[self::MU_SYMBOL]) ? $main[self::MU_SYMBOL] : 0;
        $model->pmu = isset($main[self::PMU_SYMBOL]) ? $main[self::PMU_SYMBOL] : 0;
        $model->sql = $this->getDatabaseQueries();
        throw_if(!$model->save(), new PerformanceException('data save sql error!'));
        //新增事件
        $this->event->dispatch(new PerformanceSavedEvent($main, $this->getRequestUrl()));
        return;
    }

}
