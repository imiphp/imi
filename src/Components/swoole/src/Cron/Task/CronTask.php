<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Task;

use Imi\App;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

/**
 * @Task("imiCronTask")
 */
class CronTask implements ITaskHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId): void
    {
        $success = false;
        $message = '';
        $paramData = $param->getData();
        $id = $paramData['id'] ?? null;
        $data = $paramData['data'] ?? null;
        $class = $paramData['class'] ?? null;
        try
        {
            /** @var \Imi\Cron\Contract\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
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

    /**
     * {@inheritDoc}
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
    {
    }
}
