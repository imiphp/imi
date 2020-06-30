<?php
namespace Imi\Cron\Listener;

use Imi\Event\EventParam;
use Imi\Cron\Annotation\Cron;
use Imi\Event\IEventListener;
use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cron\Consts\CronTaskType;
use Imi\Cron\Contract\ICronTask;
use Imi\Process\Annotation\Process;
use Imi\Process\IProcess;
use Imi\Process\ProcessManager;
use Imi\Task\Annotation\Task;
use Imi\Task\Interfaces\ITaskHandler;
use Imi\Task\TaskManager;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER",priority=Imi\Util\ImiPriority::IMI_MIN)
 * @Listener(eventName="IMI.CO_SERVER.START",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class Init implements IEventListener
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * @Inject("AutoRunProcessManager")
     *
     * @var \Imi\Process\AutoRunProcessManager
     */
    protected $autoRunProcessManager;

    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        // 未启用定时任务进程不初始化
        if(!$this->autoRunProcessManager->exists('CronProcess'))
        {
            return;
        }
        $cronManager = $this->cronManager;
        foreach(AnnotationManager::getAnnotationPoints(Cron::class, 'class') as $point)
        {
            /** @var Cron $cron */
            $cron = $point->getAnnotation();
            $cronManager->addCron($cron->id, $cron->type, $point->getClass(), [[
                'year'      =>  $cron->year,
                'month'     =>  $cron->month,
                'day'       =>  $cron->day,
                'week'      =>  $cron->week,
                'hour'      =>  $cron->hour,
                'minute'    =>  $cron->minute,
                'second'    =>  $cron->second,
            ]], $cron->data, $cron->maxExecutionTime, $cron->unique, $cron->redisPool, $cron->lockWaitTimeout, $cron->force);
        }
    }

}