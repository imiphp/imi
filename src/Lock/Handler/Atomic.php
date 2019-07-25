<?php
namespace Imi\Lock\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Handler\BaseLock;
use Imi\Util\AtomicManager;
use Swoole\Timer;

/**
 * Atomic 实现的多进程单机锁
 * 
 * @Bean("AtomicLock")
 */
class Atomic extends BaseLock
{
    /**
     * 配置的 Atomic 名称
     *
     * @var string
     */
    public $atomicName;

    /**
     * 同时获得锁的数量
     * 
     * @var int
     */
    public $quantity = 1;

    /**
     * 超时 timerid
     *
     * @var int
     */
    private $timeoutTimerId = null;

    /**
     * 加锁，会挂起协程
     *
     * @return boolean
     */
    protected function __lock(): bool
    {
        $result = AtomicManager::wait($this->atomicName, 0 === $this->waitTimeout ? -1 : ($this->waitTimeout / 1000));
        if($result)
        {
            $this->startTimeoutTimer();
        }
        return $result;
    }

    /**
     * 尝试获取锁
     *
     * @return boolean
     */
    protected function __tryLock(): bool
    {
        $result = AtomicManager::wait($this->atomicName, 0.001);
        if($result)
        {
            $this->startTimeoutTimer();
        }
        return $result;
    }

    /**
     * 解锁
     *
     * @return boolean
     */
    protected function __unlock(): bool
    {
        $result = AtomicManager::wakeup($this->atomicName, $this->quantity);
        $this->stopTimeoutTimer();
        return $result;
    }

    /**
     * 开启超时计时器
     *
     * @return void
     */
    private function startTimeoutTimer()
    {
        if($this->timeoutTimerId)
        {
            $this->stopTimeoutTimer();
        }
        $this->timeoutTimerId = Timer::after($this->lockExpire / 1000, function(){
            $this->unlock();
        });
    }

    /**
     * 停止超时计时器
     *
     * @return void
     */
    private function stopTimeoutTimer()
    {
        Timer::clear($this->timeoutTimerId);
        $this->timeoutTimerId = null;
    }

}