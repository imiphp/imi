<?php

namespace Imi\Cron\Process;

use Imi\App;
use Imi\Cron\Util\CronUtil;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;
use Imi\Util\Args;

/**
 * 定时任务工作进程.
 *
 * @Process(name="CronWorkerProcess")
 */
class CronWorkerProcess extends BaseProcess
{
    public function run(\Swoole\Process $process)
    {
        $success = false;
        $message = '';
        try
        {
            $id = Args::get('id');
            $data = json_decode(Args::get('data'), true);
            $class = Args::get('class');
            /** @var \Imi\Cron\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
            $success = true;
            $exitCode = 0;
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage();
            $exitCode = 1;
            throw $th;
        }
        finally
        {
            CronUtil::reportCronResult($id, $success, $message);
            $process->exit($exitCode);
        }
    }
}
