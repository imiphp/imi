<?php

declare(strict_types=1);

namespace Imi\Server\Session\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Redis\Redis as ImiRedis;

/**
 * @Bean("SessionRedis")
 */
class Redis extends Base
{
    /**
     * Redis连接池名称.
     */
    protected string $poolName = '';

    /**
     * Redis中存储的key前缀，可以用于多系统session的分离.
     */
    protected string $keyPrefix = '';

    public function __init(): void
    {
        parent::__init();
        if ('' === $this->keyPrefix)
        {
            $this->keyPrefix = 'imi:' . App::getNamespace() . ':';
        }
    }

    /**
     * 销毁session数据.
     */
    public function destroy(string $sessionId): void
    {
        ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId) {
            $redis->del($this->getKey($sessionId));
        }, $this->poolName, true);
    }

    /**
     * 垃圾回收.
     *
     * @param int $maxLifeTime 最大存活时间，单位：秒
     */
    public function gc(int $maxLifeTime): void
    {
        // 用redis数据自动过期，这里什么都不需要做
    }

    /**
     * 读取session.
     */
    public function read(string $sessionId): string
    {
        return ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId) {
            return $redis->get($this->getKey($sessionId));
        }, $this->poolName, true) ?: '';
    }

    /**
     * 写入session.
     */
    public function write(string $sessionId, string $sessionData, int $maxLifeTime): void
    {
        ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId, $sessionData, $maxLifeTime) {
            $redis->set($this->getKey($sessionId), $sessionData, $maxLifeTime);
        }, $this->poolName, true);
    }

    /**
     * 获取在Redis中存储的key.
     */
    public function getKey(string $sessionId): string
    {
        return $this->keyPrefix . $sessionId;
    }
}
