<?php

declare(strict_types=1);

namespace Imi\Cron;

/**
 * 定时任务对象
 */
class CronTask
{
    /**
     * 任务唯一ID.
     */
    private string $id = '';

    /**
     * 任务类型.
     *
     * \Imi\Cron\Consts\CronTaskType 类常量
     */
    private string $type = '';

    /**
     * 任务执行回调，可以是callable类型，也可以是 task、process 名.
     *
     * @var string|callable
     */
    private $task;

    /**
     * 定时配置.
     *
     * @var \Imi\Cron\CronRule[]
     */
    private array $cronRules = [];

    /**
     * 数据.
     *
     * @var mixed
     */
    private $data;

    /**
     * 定时任务唯一性设置
     * 当前实例唯一: current
     * 所有实例唯一: all
     * 不唯一: null.
     */
    private ?string $unique = null;

    /**
     * 用于锁的 `Redis` 连接池名.
     */
    private ?string $redisPool = null;

    /**
     * 获取锁超时时间，单位：秒.
     */
    private float $lockWaitTimeout = 0;

    /**
     * 最大运行执行时间，单位：秒。该值与分布式锁超时时间共享.
     */
    private float $maxExecutionTime = 0;

    /**
     * 获取上一次运行时间.
     */
    private int $lastRunTime = -1;

    /**
     * 每次启动服务强制执行.
     */
    private bool $force = false;

    /**
     * 构造方法.
     *
     * @param callable|string $task
     * @param mixed           $data
     */
    public function __construct(string $id, string $type, $task, array $cronRules, $data, float $maxExecutionTime = 3, ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, bool $force = false)
    {
        $this->id = $id;
        $this->type = $type;
        $this->task = $task;
        $this->cronRules = $this->parseCronRule($cronRules);
        $this->data = $data;
        $this->unique = $unique;
        $this->redisPool = $redisPool;
        $this->lockWaitTimeout = $lockWaitTimeout;
        $this->maxExecutionTime = $maxExecutionTime;
        $this->force = $force;
    }

    /**
     * 处理定时规则.
     *
     * @return \Imi\Cron\CronRule[]
     */
    private function parseCronRule(array $cronRules): array
    {
        $result = [];
        foreach ($cronRules as $rule)
        {
            $result[] = new CronRule($rule);
        }

        return $result;
    }

    /**
     * Get 任务唯一ID.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get \Imi\Cron\Consts\CronTaskType 类常量.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get 任务执行回调，可以是callable类型，也可以是 task、process 名.
     *
     * @return string|callable
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Get 定时配置.
     *
     * @return \Imi\Cron\CronRule[]
     */
    public function getCronRules(): array
    {
        return $this->cronRules;
    }

    /**
     * Get 数据.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get 在当前服务实例中唯一，只能同时执行一个.
     */
    public function getUnique(): ?string
    {
        return $this->unique;
    }

    /**
     * Get 用于锁的 `Redis` 连接池名.
     */
    public function getRedisPool(): ?string
    {
        return $this->redisPool;
    }

    /**
     * Get 获取锁超时时间，单位：秒.
     */
    public function getLockWaitTimeout(): float
    {
        return $this->lockWaitTimeout;
    }

    /**
     * 获取上一次执行时间.
     *
     * 返回秒级时间戳
     */
    public function getLastRunTime(): int
    {
        return $this->lastRunTime;
    }

    /**
     * Get 最大运行执行时间，单位：秒。该值与分布式锁超时时间共享.
     */
    public function getMaxExecutionTime(): float
    {
        return $this->maxExecutionTime;
    }

    /**
     * 更新最后执行时间.
     */
    public function updateLastRunTime(int $time): void
    {
        $this->lastRunTime = $time;
    }

    /**
     * Get 每次启动服务强制执行.
     */
    public function getForce(): bool
    {
        return $this->force;
    }
}
