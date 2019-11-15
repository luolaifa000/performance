<?php
namespace Langyi\Performance\Commands;

use Illuminate\Console\Command;

class PerformanceCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:clean {--expire=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时清除旧数据,避免数据量大';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $expire = $this->option('expire');
        $this->laravel->make('performance.manager')->clean($expire);
    }


}
