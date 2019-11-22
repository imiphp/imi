<?php
namespace Imi\Cron;

use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Imi\Cron\Traits\TWorkerReport;

/**
 * 定时任务工作类
 * @Bean("CronWorker")
 */
class CronWorker
{
    use TWorkerReport;

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
     * @param mixed $data
     * @return mixed
     */
    public function exec($id, $data)
    {
        $message = '';
        try {
            $task = $this->cronManager->getTask($id);
            if(!$task)
            {
                throw new \RuntimeException(sprintf('Can not found task %s', $id));
            }
            $taskCallable = $task->getTask();
            if(is_callable($taskCallable))
            {
                try {
                    $taskCallable($id, $data);
                    $success = true;
                } catch(\Throwable $th) {
                    throw new \RuntimeException(sprintf('Task %s execution failed, message: %s', $id, $th->getMessage()));
                }
            }
            else
            {
                throw new \RuntimeException(sprintf('Task %s does not a callable', $id));
            }
            $success = true;
        } catch(\Throwable $th) {
            $success = false;
            $message = $th->getMessage();
        } finally {
            $this->reportCronResult($id, $success, $message);
        }
    }

}
