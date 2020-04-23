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
            $class = $point->getClass();
            if(is_subclass_of($class, ICronTask::class))
            {
                switch($cronType = $cron->type)
                {
                    case CronTaskType::ALL_WORKER:
                    case CronTaskType::RANDOM_WORKER:
                        $task = function($id, $data) use($class){
                            /** @var \Imi\Cron\ICronTask $handler */
                            $handler = App::getBean($class);
                            $handler->run($id, $data);
                        };
                        break;
                    case CronTaskType::TASK:
                        $task = function($id, $data) use($class){
                            TaskManager::nPost('imiCronTask', [
                                'id'    =>  $id,
                                'data'  =>  $data,
                                'class' =>  $class,
                            ]);
                        };
                        break;
                    case CronTaskType::PROCESS:
                        $task = function($id, $data) use($class){
                            ProcessManager::run('CronWorkerProcess', [
                                'id'        =>  $id,
                                'data'      =>  json_encode($data),
                                'class'     =>  $class,
                                'cronSock'  =>  $this->cronManager->getSocketFile(),
                            ]);
                        };
                        break;
                }
            }
            else if(is_subclass_of($class, IProcess::class))
            {
                $cronType = CronTaskType::PROCESS;
                /** @var Process $process */
                $process = AnnotationManager::getClassAnnotations($class, Process::class)[0] ?? null;
                if(!$process)
                {
                    throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Process Annotation', $cron->id, $class));
                }
                $task = function($id, $data) use($process){
                    ProcessManager::run($process->name, [
                        'id'        =>  $id,
                        'data'      =>  json_encode($data),
                        'cronSock'  =>  $this->cronManager->getSocketFile(),
                    ]);
                };
            }
            else if(is_subclass_of($class, ITaskHandler::class))
            {
                $cronType = CronTaskType::TASK;
                /** @var Task $taskAnnotation */
                $taskAnnotation = AnnotationManager::getClassAnnotations($class, Task::class)[0] ?? null;
                if(!$taskAnnotation)
                {
                    throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Task Annotation', $cron->id, $class));
                }
                $task = function($id, $data) use($taskAnnotation, $cron){
                    TaskManager::nPost($taskAnnotation->name, $data);
                };
            }
            else
            {
                throw new \RuntimeException(sprintf('Invalid cron class %s', $class));
            }
            $cronManager->addCron($cron->id, $cronType, $task, [[
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