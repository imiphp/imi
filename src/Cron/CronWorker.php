<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Cron\Util\CronUtil;

/**
 * 定时任务工作类.
 *
 * @Bean("CronWorker")
 */
class CronWorker
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * 执行任务
     *
     * @param string $id
     * @param mixed  $data
     *
     * @return mixed
     */
    public function exec($id, $data, $task, $type)
    {
        $message = '';
        try
        {
            if (null === $task)
            {
                $taskObj = $this->cronManager->getTask($id);
                if (!$taskObj)
                {
                    throw new \RuntimeException(sprintf('Can not found task %s', $id));
                }
                $task = $taskObj->getTask();
            }
            $taskCallable = $this->cronManager->getTaskCallable($id, $task, $type);
            if (\is_callable($taskCallable))
            {
                try
                {
                    $taskCallable($id, $data);
                    $success = true;
                }
                catch (\Throwable $th)
                {
                    throw new \RuntimeException(sprintf('Task %s execution failed, message: %s', $id, $th->getMessage()), $th->getCode(), $th);
                }
            }
            else
            {
                throw new \RuntimeException(sprintf('Task %s does not a callable', $id));
            }
            $success = true;
        }
        catch (\Throwable $th)
        {
            $success = false;
            $message = $th->getMessage();
            throw $th;
        }
        finally
        {
            CronUtil::reportCronResult($id, $success, $message);
        }
    }
}
