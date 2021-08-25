<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Bean;
use Imi\Cli\ImiCommand;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\ICronTask;
use Imi\Cron\CronTask;
use Imi\Swoole\Cron\Consts\CronTaskType;
use Imi\Swoole\Process\Annotation\Process;
use Imi\Swoole\Process\Contract\IProcess;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskManager;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use function Yurun\Swoole\Coroutine\goWait;

/**
 * 定时任务管理器.
 *
 * @Bean("CronManager")
 */
class CronManager implements ICronManager
{
    /**
     * 注入的任务列表.
     */
    protected array $tasks = [];

    /**
     * socket 文件路径.
     *
     * 不支持 samba 文件共享
     */
    protected ?string $socketFile = null;

    /**
     * 真实的任务对象列表.
     *
     * @var \Imi\Cron\CronTask[]
     */
    private array $realTasks = [];

    public function __init(): void
    {
        if (null === $this->socketFile)
        {
            if (ProcessType::PROCESS === App::get(ProcessAppContexts::PROCESS_TYPE))
            {
                $input = ImiCommand::getInput();
                $this->socketFile = $input->getParameterOption('--cron-sock');
                if (!$this->socketFile)
                {
                    throw new \InvalidArgumentException('In process to run cron, you must have arg cron-sock');
                }
            }
            else
            {
                $this->socketFile = '/tmp/imi.' . App::get(ProcessAppContexts::MASTER_PID) . '.cron.sock';
            }
        }
        $realTasks = &$this->realTasks;
        foreach ($this->tasks as $id => $task)
        {
            $realTasks[$id] = new CronTask($id, $task['type'], $task['task'], $task['cron'], $task['data'] ?? null, $task['lockExpire'] ?? 120, $task['unique'] ?? false, $task['redisPool'] ?? null, $task['lockWaitTimeout'] ?? 10, $task['force'] ?? false);
        }
    }

    /**
     * 使用注解增加定时任务
     */
    public function addCronByAnnotation(Cron $cron, string $pointClass): void
    {
        $this->addCron($cron->id, $cron->type, $pointClass, [[
            'year'      => $cron->year,
            'month'     => $cron->month,
            'day'       => $cron->day,
            'week'      => $cron->week,
            'hour'      => $cron->hour,
            'minute'    => $cron->minute,
            'second'    => $cron->second,
        ]], $cron->data, $cron->maxExecutionTime, $cron->unique, $cron->redisPool, $cron->lockWaitTimeout, $cron->force);
    }

    /**
     * 增加定时任务
     *
     * @param callable|string $task
     * @param mixed           $data
     */
    public function addCron(string $id, ?string $type, $task, array $cronRules, $data, float $lockExpire = 3, ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, bool $force = false): void
    {
        if (isset($this->tasks[$id]))
        {
            throw new \RuntimeException(sprintf('Cron id %s already exists', $id));
        }
        if (null === $type && \is_string($task))
        {
            $type = $this->getCronTypeByClass($task);
        }
        if (null === $type)
        {
            throw new \InvalidArgumentException('$type must not null');
        }
        $this->realTasks[$id] = new CronTask($id, $type, $task, $cronRules, $data, $lockExpire, $unique, $redisPool, $lockWaitTimeout, $force);
    }

    /**
     * 移除定时任务
     */
    public function removeCron(string $id): void
    {
        if (isset($this->tasks[$id]))
        {
            unset($this->tasks[$id], $this->realTasks[$id]);
        }
    }

    /**
     * 清空定时任务
     */
    public function clear(): void
    {
        $this->tasks = $this->realTasks = [];
    }

    /**
     * Get 真实的任务对象列表.
     *
     * @return \Imi\Cron\CronTask[]
     */
    public function getRealTasks(): array
    {
        return $this->realTasks;
    }

    /**
     * 获取任务对象
     *
     * @param string $id
     */
    public function getTask($id): ?CronTask
    {
        return $this->realTasks[$id] ?? null;
    }

    /**
     * socket 文件路径.
     */
    public function getSocketFile(): string
    {
        return $this->socketFile;
    }

    /**
     * 使用类名获取定时任务类型.
     */
    public function getCronTypeByClass(string $class): ?string
    {
        if (is_subclass_of($class, IProcess::class))
        {
            return CronTaskType::PROCESS;
        }
        elseif (is_subclass_of($class, ITaskHandler::class))
        {
            return CronTaskType::TASK;
        }

        return null;
    }

    /**
     * 获取任务回调.
     *
     * @return string|callable
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType)
    {
        $task = $class;
        if (is_subclass_of($class, ICronTask::class))
        {
            switch ($cronType)
            {
                case CronTaskType::ALL_WORKER:
                case CronTaskType::RANDOM_WORKER:
                    $task = function (string $id, $data) use ($class) {
                        /** @var \Imi\Cron\Contract\ICronTask $handler */
                        $handler = App::getBean($class);
                        $handler->run($id, $data);
                    };
                    break;
                case CronTaskType::TASK:
                    $task = function (string $id, $data) use ($class) {
                        TaskManager::nPost('imiCronTask', [
                            'id'    => $id,
                            'data'  => $data,
                            'class' => $class,
                        ]);
                    };
                    break;
                case CronTaskType::PROCESS:
                    $task = function (string $id, $data) use ($class) {
                        ProcessManager::run('CronWorkerProcess', [
                            'id'         => $id,
                            'data'       => json_encode($data),
                            'class'      => $class,
                            'cron-sock'  => $this->getSocketFile(),
                        ]);
                    };
                    break;
                case CronTaskType::CRON_PROCESS:
                    return function (string $id, $data) use ($class) {
                        goWait(function () use ($class, $id, $data) {
                            /** @var \Imi\Cron\Contract\ICronTask $handler */
                            $handler = App::getBean($class);
                            $handler->run($id, $data);
                        });
                    };
            }
        }
        elseif (is_subclass_of($class, IProcess::class))
        {
            $cronType = CronTaskType::PROCESS;
            /** @var Process|null $process */
            $process = AnnotationManager::getClassAnnotations($class, Process::class)[0] ?? null;
            if (!$process)
            {
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Process Annotation', $cronId, $class));
            }
            $task = function (string $id, $data) use ($process) {
                ProcessManager::run($process->name, [
                    'id'         => $id,
                    'data'       => json_encode($data),
                    'cron-sock'  => $this->getSocketFile(),
                ]);
            };
        }
        elseif (is_subclass_of($class, ITaskHandler::class))
        {
            $cronType = CronTaskType::TASK;
            /** @var Task|null $taskAnnotation */
            $taskAnnotation = AnnotationManager::getClassAnnotations($class, Task::class)[0] ?? null;
            if (!$taskAnnotation)
            {
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Task Annotation', $cronId, $class));
            }
            $task = function (string $id, $data) use ($taskAnnotation) {
                TaskManager::nPost($taskAnnotation->name, $data);
            };
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Invalid cron class %s', $class));
        }

        return $task;
    }
}
