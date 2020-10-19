<?php

namespace Imi\Cron;

/**
 * 定时任务对象
 */
class CronTask
{
    /**
     * 任务唯一ID.
     *
     * @var string
     */
    private $id;

    /**
     * 任务类型.
     *
     * \Imi\Cron\Consts\CronTaskType 类常量
     *
     * @var string
     */
    private $type;

    /**
     * 任务执行回调，可以是callable类型，也可以是 task、process 名.
     *
     * @var string
     */
    private $task;

    /**
     * 定时配置.
     *
     * @var \Imi\Cron\CronRule[]
     */
    private $cronRules;

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
     *
     * @var string
     */
    private $unique;

    /**
     * 用于锁的 `Redis` 连接池名.
     *
     * @var string
     */
    private $redisPool;

    /**
     * 获取锁超时时间，单位：秒.
     *
     * @var float
     */
    private $lockWaitTimeout;

    /**
     * 最大运行执行时间，单位：秒。该值与分布式锁超时时间共享.
     *
     * @var float
     */
    private $maxExecutionTime;

    /**
     * 获取上一次运行时间.
     *
     * @var int
     */
    private $lastRunTime = -1;

    /**
     * 每次启动服务强制执行.
     *
     * @var bool
     */
    private $force = false;

    /**
     * 构造方法.
     *
     * @param string          $id
     * @param string          $type
     * @param callable|string $task
     * @param array           $cronRules
     * @param mixed           $data
     * @param float           $maxExecutionTime
     * @param string|null     $unique
     * @param string|null     $redisPool
     * @param float           $lockWaitTimeout
     * @param bool            $force
     */
    public function __construct(string $id, string $type, $task, array $cronRules, $data, float $maxExecutionTime = 3, $unique = null, $redisPool = null, float $lockWaitTimeout = 3, bool $force = false)
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
     * @param array $cronRules
     *
     * @return \Imi\Cron\CronRule[]
     */
    private function parseCronRule($cronRules)
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
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get \Imi\Cron\Consts\CronTaskType 类常量.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get 任务执行回调，可以是callable类型，也可以是 task、process 名.
     *
     * @return string
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
    public function getCronRules()
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
     *
     * @return bool
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Get 用于锁的 `Redis` 连接池名.
     *
     * @return string
     */
    public function getRedisPool()
    {
        return $this->redisPool;
    }

    /**
     * Get 获取锁超时时间，单位：秒.
     *
     * @return float
     */
    public function getLockWaitTimeout()
    {
        return $this->lockWaitTimeout;
    }

    /**
     * 获取上一次执行时间.
     *
     * 返回秒级时间戳
     *
     * @return void
     */
    public function getLastRunTime()
    {
        return $this->lastRunTime;
    }

    /**
     * Get 最大运行执行时间，单位：秒。该值与分布式锁超时时间共享.
     *
     * @return float
     */
    public function getMaxExecutionTime()
    {
        return $this->maxExecutionTime;
    }

    /**
     * 更新最后执行时间.
     *
     * @param int $time
     *
     * @return void
     */
    public function updateLastRunTime($time)
    {
        $this->lastRunTime = $time;
    }

    /**
     * Get 每次启动服务强制执行.
     *
     * @return bool
     */
    public function getForce()
    {
        return $this->force;
    }
}
