<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Util\Args;
use Imi\Process\IProcess;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Traits\TWorkerReport;
use Imi\Process\Annotation\Process;

/**
 * @Cron(id="CronProcess", second="3n")
 * @Process("CronProcess1")
 */
class TaskProcess implements IProcess
{
    use TWorkerReport;

    public function run(\Swoole\Process $process)
    {
        $id = Args::get('id');
        $data = json_decode(Args::get('data'), true);
        $this->reportCronResult($id, true, '');
        $process->exit(0);
    }

}