<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Consts\CronTaskType;
use Imi\Cron\Contract\ICronManager;
use Imi\Cron\Contract\ICronTask;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * 定时任务管理器.
 *
 * @Bean("CronManager")
 */
class CronManager implements ICronManager
{
    /**
     * 注入的任务列表.
     *
     * @var array
     */
    protected array $tasks = [];

    /**
     * socket 文件路径.
     *
     * 不支持 samba 文件共享
     *
     * @var string|null
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
                $input = new ArgvInput();
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
     *
     * @param \Imi\Cron\Annotation\Cron $cron
     * @param string                    $pointClass
     *
     * @return void
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
     * @param string          $id
     * @param string|null     $type
     * @param callable|string $task
     * @param array           $cronRules
     * @param mixed           $data
     * @param float           $lockExpire
     * @param string|null     $unique
     * @param string|null     $redisPool
     * @param float           $lockWaitTimeout
     * @param bool            $force
     *
     * @return void
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
     *
     * @param string $id
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return \Imi\Cron\CronTask|null
     */
    public function getTask($id): ?CronTask
    {
        return $this->realTasks[$id] ?? null;
    }

    /**
     * socket 文件路径.
     *
     * @return string
     */
    public function getSocketFile(): string
    {
        return $this->socketFile;
    }

    /**
     * 使用类名获取定时任务类型.
     *
     * @param string $class
     *
     * @return string|null
     */
    public function getCronTypeByClass(string $class): ?string
    {
        return null;
    }

    /**
     * 获取任务回调.
     *
     * @param string      $cronId
     * @param string      $class
     * @param string|null $cronType
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
                case CronTaskType::CRON_PROCESS:
                    return function (string $id, $data) use ($class) {
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
