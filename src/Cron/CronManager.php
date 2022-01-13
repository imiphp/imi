<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Cli\ImiCommand;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Consts\CronTaskType;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\ICronTask;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

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
            $realTasks[$id] = new CronTask($id, $task['type'], $task['task'], $task['cron'], $task['data'] ?? null, $task['lockExpire'] ?? 120, $task['unique'] ?? null, $task['redisPool'] ?? null, $task['lockWaitTimeout'] ?? 10, $task['force'] ?? false);
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
    public function getRealTasks(): array
    {
        return $this->realTasks;
    }

    /**
     * {@inheritDoc}
     */
    public function getTask($id): ?CronTask
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
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getTaskCallable(string $cronId, string $class, ?string &$cronType)
    {
        $task = $class;
        if (is_subclass_of($class, ICronTask::class))
        {
            switch ($cronType)
            {
                case CronTaskType::CRON_PROCESS:
                    return static function (string $id, $data) use ($class) {
                        /** @var \Imi\Cron\Contract\ICronTask $handler */
                        $handler = App::getBean($class);
                        $handler->run($id, $data);
                    };
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Invalid cron class %s', $class));
        }

        return $task;
    }
}
