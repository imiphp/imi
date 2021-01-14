<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\IProcess;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @Cron(id="TaskProcess1", second="3n")
 * @Process("TaskProcess1")
 */
class TaskProcess implements IProcess
{
    public function run(\Swoole\Process $process)
    {
        $success = false;
        $message = '';
        try
        {
            $input = new ArgvInput();
            $id = $input->getParameterOption('--id');
            if (false === $id)
            {
                return;
            }
            $data = json_decode($input->getParameterOption('--data'), true);
            $success = true;
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage();
            throw $th;
        }
        finally
        {
            CronUtil::reportCronResult($id, $success, $message);
        }
    }
}
