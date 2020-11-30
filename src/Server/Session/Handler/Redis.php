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
     *
     * @var string
     */
    protected string $poolName;

    /**
     * Redis中存储的key前缀，可以用于多系统session的分离.
     *
     * @var string
     */
    protected string $keyPrefix;

    public function __init()
    {
        parent::__init();
        if (!isset($this->keyPrefix))
        {
            $this->keyPrefix = 'imi:' . App::getNamespace() . ':';
        }
    }

    /**
     * 销毁session数据.
     *
     * @param string $sessionId
     *
     * @return void
     */
    public function destroy(string $sessionId)
    {
        ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId) {
            $redis->del($this->getKey($sessionId));
        }, $this->poolName, true);
    }

    /**
     * 垃圾回收.
     *
     * @param int $maxLifeTime 最大存活时间，单位：秒
     *
     * @return void
     */
    public function gc(int $maxLifeTime)
    {
        // 用redis数据自动过期，这里什么都不需要做
    }

    /**
     * 读取session.
     *
     * @param string $sessionId
     *
     * @return mixed
     */
    public function read(string $sessionId)
    {
        return ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId) {
            return $redis->get($this->getKey($sessionId));
        }, $this->poolName, true);
    }

    /**
     * 写入session.
     *
     * @param string $sessionId
     * @param string $sessionData
     * @param int    $maxLifeTime
     *
     * @return void
     */
    public function write(string $sessionId, string $sessionData, int $maxLifeTime)
    {
        ImiRedis::use(function (\Imi\Redis\RedisHandler $redis) use ($sessionId, $sessionData, $maxLifeTime) {
            $redis->set($this->getKey($sessionId), $sessionData, $maxLifeTime);
        }, $this->poolName, true);
    }

    /**
     * 获取在Redis中存储的key.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function getKey(string $sessionId): string
    {
        return $this->keyPrefix . $sessionId;
    }
}
