<?php
namespace Imi\Cron\Process;

use Imi\App;
use Imi\Util\Args;
use Imi\Process\BaseProcess;
use Imi\Cron\Traits\TWorkerReport;
use Imi\Process\Annotation\Process;

/**
 * 定时任务工作进程
 * 
 * @Process(name="CronWorkerProcess")
 */
class CronWorkerProcess extends BaseProcess
{
    use TWorkerReport;

    public function run(\Swoole\Process $process)
    {
        $success = false;
        $message = '';
        try {
            $id = Args::get('id');
            $data = json_decode(Args::get('data'), true);
            $class = Args::get('class');
            /** @var \Imi\Cron\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
            $success = true;
            $exitCode = 0;
        } catch(\Throwable $th) {
            $message = $th->getMessage();
            $exitCode = 1;
            throw $th;
        } finally {
            $this->reportCronResult($id, $success, $message);
            $process->exit($exitCode);
        }
    }

}
