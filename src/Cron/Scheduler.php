<?php
namespace Imi\Cron;

use Imi\Log\Log;
use Imi\ServerManage;
use Imi\Cron\Message\Result;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Yurun\Swoole\CoPool\CoPool;
use Imi\Cron\Consts\CronTaskType;
use Yurun\Swoole\CoPool\Interfaces\ICoTask;
use Yurun\Swoole\CoPool\Interfaces\ITaskParam;

/**
 * @Bean("CronScheduler")
 * 
 * 定时任务调度器
 */
class Scheduler
{
    /**
     * @Inject("CronManager")
     *
     * @var \Imi\Cron\CronManager
     */
    protected $cronManager;

    /**
     * @Inject("CronCalculator")
     *
     * @var \Imi\Cron\CronCalculator
     */
    protected $cronCalculator;

    /**
     * @Inject("CronLock")
     *
     * @var \Imi\Cron\CronLock
     */
    protected $cronLock;

    /**
     * 协程工作池的协程数量
     *
     * @var integer
     */
    protected $poolCoCount = 16;

    /**
     * 协程工作池的队列长度
     *
     * @var integer
     */
    protected $poolQueueLength = 1024;

    /**
     * 协程工作池
     *
     * @var \Yurun\Swoole\CoPool\CoPool
     */
    private $coPool;

    /**
     * 下次执行时间集合
     *
     * @var array
     */
    private $nextTickTimeMap = [];

    /**
     * 正在执行的任务列表
     *
     * @var \Imi\Cron\CronTask[]
     */
    private $runningTasks = [];

    /**
     * 是否首轮运行
     *
     * @var boolean
     */
    private $first = true;

    public function __construct()
    {
        $this->coPool = $coPool = new CoPool($this->poolCoCount, $this->poolQueueLength,
            // 定义任务匿名类，当然你也可以定义成普通类，传入完整类名
            new class implements ICoTask
            {
                /**
                 * 执行任务
                 *
                 * @param ITaskParam $param
                 * @return mixed
                 */
                public function run(ITaskParam $param)
                {
                    /** @var \Imi\Cron\CronTask $task */
                    $task = $param->getData();
                    switch($task->getType())
                    {
                        case CronTaskType::RANDOM_WORKER:
                            $swooleServer = ServerManage::getServer('main')->getSwooleServer();
                            $swooleServer->sendMessage(json_encode([
                                'action'    =>  'cronTask',
                                'id'        =>  $task->getId(),
                                'data'      =>  $task->getData(),
                            ]), mt_rand(0, $swooleServer->setting['worker_num'] - 1));
                            break;
                        case CronTaskType::ALL_WORKER:
                            $swooleServer = ServerManage::getServer('main')->getSwooleServer();
                            $message = json_encode([
                                'action'    =>  'cronTask',
                                'id'        =>  $task->getId(),
                                'data'      =>  $task->getData(),
                            ]);
                            for($i = 0; $i < $swooleServer->setting['worker_num']; ++$i)
                            {
                                $swooleServer->sendMessage($message, $i);
                            }
                            break;
                        case CronTaskType::TASK:
                            ($task->getTask())($task->getId(), $task->getData());
                            break;
                        case CronTaskType::PROCESS:
                            ($task->getTask())($task->getId(), $task->getData());
                            break;
                    }
                }
    
            }
        );
        // 运行协程池
        $coPool->run();
    }

    /**
     * 遍历可运行的任务列表
     *
     * @return \Imi\Cron\CronTask[]
     */
    public function schedule()
    {
        $now = time();
        $runningTasks = &$this->runningTasks;
        $nextTickTimeMap = &$this->nextTickTimeMap;
        $cronCalculator = $this->cronCalculator;
        $first = &$this->first;
        foreach($this->cronManager->getRealTasks() as $task)
        {
            $id = $task->getId();
            if(isset($runningTasks[$id]))
            {
                if(time() < $task->getLastRunTime() + $task->getMaxExecutionTime())
                {
                    continue;
                }
                else
                {
                    unset($runningTasks[$id]);
                }
            }
            if(!isset($nextTickTimeMap[$id]))
            {
                $nextTickTimeMap[$id] = $cronCalculator->getNextTickTime($task->getLastRunTime(), $task->getCronRules());
            }
            if(($first && $task->getForce()) || $now >= $nextTickTimeMap[$id])
            {
                unset($nextTickTimeMap[$id]);
                yield $task;
            }
        }
        $first = false;
    }

    /**
     * 执行任务
     *
     * @param \Imi\Cron\CronTask $task
     * @return void
     */
    public function runTask($task)
    {
        if(!$this->cronLock->lock($task))
        {
            Log::error(sprintf('Task %s lock failed', $task->getId()));
            return;
        }
        $task->updateLastRunTime(time());
        $this->runningTasks[$task->getId()] = $task;
        $this->coPool->addTaskAsync($task);
    }

    /**
     * 完成任务
     *
     * @param \Imi\Cron\Message\Result $result
     * @return void
     */
    public function completeTask(Result $result)
    {
        $runningTasks = &$this->runningTasks;
        if(isset($runningTasks[$result->id]))
        {
            if(!$this->cronLock->unlock($runningTasks[$result->id]))
            {
                Log::error(sprintf('Task %s unlock failed', $result->id));
            }
            unset($runningTasks[$result->id]);
        }
        if($result->success)
        {
            Log::info(sprintf('Task: %s, Process: %s#%s, Success', $result->id, $result->processType, $result->processId));
        }
        else
        {
            Log::error(sprintf('Task: %s, Process: %s#%s, %s', $result->id, $result->processType, $result->processId, $result->message));
        }
    }

}
