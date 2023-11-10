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
 */
#[Bean(name: 'CronManager', recursion: false)]
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
     * 任务进程支持回显终端输出.
     */
    protected bool $stdOutput = true;

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
                $this->socketFile = $input->getParameterOption('--cron-sock', null);
            }
            if (null === $this->socketFile)
            {
                $process = ProcessManager::getProcessWithManager('CronProcess');
                if ($process)
                {
                    $this->socketFile = $process->getUnixSocketFile();
                }
            }
            if (null === $this->socketFile)
            {
                throw new \InvalidArgumentException('In process to run cron, you must have arg cron-sock');
            }
        }
        $realTasks = &$this->realTasks;
        foreach ($this->tasks as $id => $task)
        {
            $realTasks[$id] = new CronTask((string) $id, $task['type'], $task['task'], $task['cron'], $task['data'] ?? null, $task['lockExpire'] ?? 120, $task['unique'] ?? null, $task['redisPool'] ?? null, $task['lockWaitTimeout'] ?? 10, $task['force'] ?? false, $task['successLog'] ?? true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addCronByAnnotation(Cron $cron, string $pointClass): void
    {
        $this->addCron($cron->id, $cron->type, $pointClass, [[
            'year'       => $cron->year,
            'month'      => $cron->month,
            'day'        => $cron->day,
            'week'       => $cron->week,
            'hour'       => $cron->hour,
            'minute'     => $cron->minute,
            'second'     => $cron->second,
            'delayMin'   => $cron->delayMin,
            'delayMax'   => $cron->delayMax,
        ]], $cron->data, $cron->maxExecutionTime, $cron->unique, $cron->redisPool, $cron->lockWaitTimeout, $cron->force, $cron->successLog);
    }

    /**
     * {@inheritDoc}
     */
    public function addCron(string $id, ?string $type, callable|string $task, array $cronRules, mixed $data, float $lockExpire = 3, ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, bool $force = false, bool $successLog = true): void
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
        $this->realTasks[$id] = new CronTask($id, $type, $task, $cronRules, $data, $lockExpire, $unique, $redisPool, $lockWaitTimeout, $force, $successLog);
    }

    /**
     * {@inheritDoc}
     */
    public function removeCron(string $id): void
    {
        if (isset($this->tasks[$id]))
        {
            unset($this->tasks[$id]);
        }
        if (isset($this->realTasks[$id]))
        {
            unset($this->realTasks[$id]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->tasks = $this->realTasks = [];
    }

    /**
     * {@inheritDoc}
     */
    public function hasTask(string $id): bool
    {
        return isset($this->realTasks[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function getRealTasks(): array
    {
        return $this->realTasks;
    }

    /**
     * {@inheritDoc}
     */
    public function getTask(string $id): ?CronTask
    {
        return $this->realTasks[$id] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSocketFile(): string
    {
        return $this->socketFile;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType): string|callable
    {
        $task = $class;
        if (is_subclass_of($class, ICronTask::class))
        {
            switch ($cronType)
            {
                case CronTaskType::ALL_WORKER:
                case CronTaskType::RANDOM_WORKER:
                    $task = static function (string $id, $data) use ($class): void {
                        /** @var \Imi\Cron\Contract\ICronTask $handler */
                        $handler = App::getBean($class);
                        $handler->run($id, $data);
                    };
                    break;
                case CronTaskType::TASK:
                    $task = static function (string $id, $data) use ($class): void {
                        TaskManager::nPost('imiCronTask', [
                            'id'    => $id,
                            'data'  => $data,
                            'class' => $class,
                        ]);
                    };
                    break;
                case CronTaskType::PROCESS:
                    $task = function (string $id, $data) use ($class): void {
                        ProcessManager::run('CronWorkerProcess', [
                            'id'         => $id,
                            'data'       => json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE),
                            'class'      => $class,
                            'cron-sock'  => $this->getSocketFile(),
                        ], null, null, $this->stdOutput);
                    };
                    break;
                case CronTaskType::CRON_PROCESS:
                    return static fn (string $id, $data) => goWait(static function () use ($class, $id, $data): void {
                        /** @var \Imi\Cron\Contract\ICronTask $handler */
                        $handler = App::getBean($class);
                        $handler->run($id, $data);
                    }, -1, true);
            }
        }
        elseif (is_subclass_of($class, IProcess::class))
        {
            $cronType = CronTaskType::PROCESS;
            /** @var Process|null $process */
            $process = AnnotationManager::getClassAnnotations($class, Process::class, true, true);
            if (!$process)
            {
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Process Annotation', $cronId, $class));
            }
            $task = function (string $id, $data) use ($process): void {
                ProcessManager::run($process->name, [
                    'id'         => $id,
                    'data'       => json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE),
                    'cron-sock'  => $this->getSocketFile(),
                ], null, null, $this->stdOutput);
            };
        }
        elseif (is_subclass_of($class, ITaskHandler::class))
        {
            $cronType = CronTaskType::TASK;
            /** @var Task|null $taskAnnotation */
            $taskAnnotation = AnnotationManager::getClassAnnotations($class, Task::class, true, true);
            if (!$taskAnnotation)
            {
                throw new \RuntimeException(sprintf('Cron %s, class %s must have a @Task Annotation', $cronId, $class));
            }
            $task = static function (string $id, $data) use ($taskAnnotation): void {
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
