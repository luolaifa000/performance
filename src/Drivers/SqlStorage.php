<?php
namespace Langyi\Performance\Drivers;

use Langyi\Performance\Models\Performance as PModel;

class SqlStorage extends AbstractDriver
{
    
    public function save(array $xhprof_data = []): void
    {
        
        if (!isset($xhprof_data['main()'])) {
            return ;
        }
        $main = $xhprof_data['main()'];
        $xhprof_data = serialize($xhprof_data);
        $model = new PModel();
        $model->url = $this->getRequestUrl();
        $model->uri = explode('?', $this->getRequestUri())[0];
        $model->uid = $this->getUid();
        $model->content = $xhprof_data;
        $model->ct = $main['ct'];
        $model->wt = $main['wt'];
        $model->cpu = $main['cpu'];
        $model->mu = $main['mu'];
        $model->pmu = $main['pmu'];
        $model->sql = $this->getDatabaseQueries();
        $model->save();
        return;
    }

}
