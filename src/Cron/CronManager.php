<?php
namespace Imi\Cron;

use Imi\App;
use Imi\Util\Args;
use Imi\Process\IProcess;
use Imi\Task\TaskManager;
use Imi\Bean\Annotation\Bean;
use Imi\Task\Annotation\Task;
use Imi\Process\ProcessManager;
use Imi\Cron\Contract\ICronTask;
use Imi\Cron\Consts\CronTaskType;
use Imi\Util\Process\ProcessType;
use Imi\Process\Annotation\Process;
use Imi\Task\Interfaces\ITaskHandler;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * 定时任务管理器
 * 
 * @Bean("CronManager")
 */
class CronManager
{
    /**
     * 注入的任务列表
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * socket 文件路径
     * 
     * 不支持 samba 文件共享
     *
     * @var string
     */
    protected $socketFile;

    /**
     * 真实的任务对象列表
     *
     * @var \Imi\Cron\CronTask[]
     */
    private $realTasks;

    public function __init()
    {
        if(null === $this->socketFile)
        {
            if(ProcessType::PROCESS === App::get(ProcessAppContexts::PROCESS_TYPE))
            {
                $this->socketFile = Args::get('cronSock');
                if(!$this->socketFile)
                {
                    throw new \InvalidArgumentException('In process to run cron, you must have arg cronSock');
                }
            }
            else
            {
                $this->socketFile = '/tmp/imi.' . App::get(ProcessAppContexts::MASTER_PID) . '.cron.sock';
            }
        }
        $this->realTasks = [];
        $realTasks = &$this->realTasks;
        foreach($this->tasks as $id => $task)
        {
            $realTasks[$id] = new CronTask($id, $task['type'], $task['task'], $task['cron'], $task['data'] ?? null, $task['lockExpire'] ?? 120, $task['unique'] ?? false, $task['redisPool'] ?? null, $task['lockWaitTimeout'] ?? 10, $task['force'] ?? false);
        }
    }

    /**
     * 增加 Cron 任务
     *
     * @param string $id
     * @param string|null $type
     * @param callable|string $task
     * @param array $cronRules
     * @param mixed $data
     * @param float $lockExpire
     * @param string|null $unique
     * @param string|null $redisPool
     * @param float $lockWaitTimeout
     * @param bool $force
     * @return void
     */
    public function addCron(string $id, ?string $type, $task, array $cronRules, $data, float $lockExpire = 3, $unique = null, $redisPool = null, float $lockWaitTimeout = 3, bool $force = false)
    {
        if(isset($this->tasks[$id]))
        {
            throw new \RuntimeException(sprintf('Cron id %s already exists', $id));
        }
        if(is_string($task))
        {
            $task = $this->getTaskCallable($id, $task, $type);
        }
        if(null === $type)
        {
            throw new \InvalidArgumentException('$type must not null');
        }
        $this->realTasks[$id] = new CronTask($id, $type, $task, $cronRules, $data, $lockExpire, $unique, $redisPool, $lockWaitTimeout, $force);
    }

    /**
     * 移除定时任务
     *
     * @param string $id
     * @return void
     */
    public function removeCron(string $id)
    {
        if(isset($this->tasks[$id]))
        {
            unset($this->tasks[$id], $this->realTasks[$id]);
        }
    }

    /**
     * Get 真实的任务对象列表
     *
     * @return \Imi\Cron\CronTask[]
     */ 
    public function getRealTasks()
    {
        return $this->realTasks;
    }

    /**
     * 获取任务对象
     *
     * @param string $id
     * @return \Imi\Cron\CronTask|null
     */
    public function getTask($id)
    {
        return $this->realTasks[$id] ?? null;
    }

    /**
     * socket 文件路径
     *
     * @return string
     */ 
    public function getSocketFile()
    {
        return $this->socketFile;
    }

    /**
     * 获取任务回调
     *
     * @param string $cronId
     * @param string $class
     * @param string|null $cronType
     * @return callable
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType): callable
    {
        if(is_subclass_of($class, ICronTask::class))
        {
            switch($cronType)
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
                            'cronSock'  =>  $this->getSocketFile(),
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
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Process Annotation', $cronId, $class));
            }
            $task = function($id, $data) use($process){
                ProcessManager::run($process->name, [
                    'id'        =>  $id,
                    'data'      =>  json_encode($data),
                    'cronSock'  =>  $this->getSocketFile(),
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
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Task Annotation', $cronId, $class));
            }
            $task = function($id, $data) use($taskAnnotation){
                TaskManager::nPost($taskAnnotation->name, $data);
            };
        }
        else
        {
            throw new \RuntimeException(sprintf('Invalid cron class %s', $class));
        }
        return $task;
    }

}
