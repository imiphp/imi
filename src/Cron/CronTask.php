<?php

declare(strict_types=1);

namespace Imi\Cron;

/**
 * 定时任务对象
 */
use Imi\Cron\Consts\UniqueLevel;

class CronTask
{
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
     * 获取上一次运行时间.
     */
    private int $lastRunTime = -1;

    /**
     * 构造方法.
     */
    public function __construct(
        /**
         * 任务唯一ID.
         */
        private readonly string $id,
        /**
         * 任务类型.
         *
         * \Imi\Cron\Consts\CronTaskType 类常量
         */
        private readonly string $type, callable|string $task, array $cronRules,
        /**
         * 数据.
         */
        private readonly mixed $data,
        /**
         * 最大运行执行时间，单位：秒。该值与分布式锁超时时间共享.
         */
        private readonly float $maxExecutionTime = 3,
        /**
         * 定时任务唯一性设置.
         */
        private readonly ?UniqueLevel $unique = null,
        /**
         * 用于锁的 `Redis` 连接池名.
         */
        private readonly ?string $redisPool = null,
        /**
         * 获取锁超时时间，单位：秒.
         */
        private readonly float $lockWaitTimeout = 3,
        /**
         * 每次启动服务强制执行.
         */
        private readonly bool $force = false,
        /**
         * 是否记录成功日志.
         */
        private readonly bool $successLog = true)
    {
        $this->task = $task;
        $this->cronRules = $this->parseCronRule($cronRules);
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
     */
    public function getTask(): string|callable
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
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Get 在当前服务实例中唯一，只能同时执行一个.
     */
    public function getUnique(): ?UniqueLevel
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

    /**
     * Get 是否记录成功日志.
     */
    public function getSuccessLog(): bool
    {
        return $this->successLog;
    }
}
