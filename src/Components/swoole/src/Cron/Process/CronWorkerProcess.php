<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Process;

use Imi\App;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\BaseProcess;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * 定时任务工作进程.
 *
 * @Process(name="CronWorkerProcess")
 */
class CronWorkerProcess extends BaseProcess
{
    public function run(\Swoole\Process $process): void
    {
        $success = false;
        $message = '';
        $input = new ArgvInput();
        $id = $input->getParameterOption('--id');
        try
        {
            $exitCode = 0;
            $data = json_decode($input->getParameterOption('--data'), true);
            $class = $input->getParameterOption('--class');
            /** @var \Imi\Cron\Contract\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
            $success = true;
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
