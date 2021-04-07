<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\BinderHandler;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\Redis\Redis as ImiRedis;
use Imi\Redis\RedisHandler;
use Imi\Worker;

/**
 * Redis 连接绑定器.
 *
 * @Bean("ConnectionBinderRedis")
 */
class Redis implements IHandler
{
    /**
     * Redis 连接池名称.
     */
    protected ?string $redisPool = null;

    /**
     * redis中第几个库.
     */
    protected int $redisDb = 0;

    /**
     * 键.
     */
    protected string $key = 'imi:connectionBinder:map';

    /**
     * @return void
     */
    public function __init()
    {
        if (0 === Worker::getWorkerID())
        {
            $this->useRedis(function (RedisHandler $redis) {
                $key = $this->key;
                $redis->del($key);
                $keys = [];
                $count = 0;
                foreach ($redis->scanEach($key . ':*') as $key)
                {
                    $keys[] = $key;
                    if (++$count >= 1000)
                    {
                        $redis->del($keys);
                        $keys = [];
                        $count = 0;
                    }
                }
                if ($keys)
                {
                    $redis->del($keys);
                }
            });
        }
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        ConnectContext::set('__flag', $flag, $clientId);
        $this->useRedis(function (RedisHandler $redis) use ($flag, $clientId) {
            $redis->hSet($this->key, $flag, $clientId);
        });
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string $clientId
     */
    public function bindNx(string $flag, $clientId): bool
    {
        $result = $this->useRedis(function (RedisHandler $redis) use ($flag, $clientId) {
            return $redis->hSetNx($this->key, $flag, $clientId);
        });
        if ($result)
        {
            ConnectContext::set('__flag', $flag, $clientId);
        }

        return $result;
    }

    /**
     * 取消绑定.
     *
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, ?int $keepTime = null): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($flag, $keepTime) {
            $key = $this->key;
            if ($clientId = $redis->hGet($key, $flag))
            {
                ConnectContext::set('__flag', null, $clientId);
            }
            $redis->multi();
            $redis->hDel($key, $flag);
            if ($clientId && $keepTime > 0)
            {
                $redis->set($key . ':old:' . $flag, $clientId, $keepTime);
            }
            $redis->exec();
        });
    }

    /**
     * 使用标记获取连接编号.
     *
     * @return int|string|null
     */
    public function getClientIdByFlag(string $flag)
    {
        return $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->hGet($this->key, $flag) ?: null;
        });
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]|string[]
     */
    public function getClientIdsByFlags(array $flags): array
    {
        return $this->useRedis(function (RedisHandler $redis) use ($flags) {
            return $redis->hMget($this->key, $flags);
        });
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string
    {
        return ConnectContext::get('__flag', null, $clientId);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[]|string[] $clientIds
     *
     * @return string[]
     */
    public function getFlagsByClientIds(array $clientIds): array
    {
        $flags = [];
        foreach ($clientIds as $clientId)
        {
            $flags[$clientId] = ConnectContext::get('__flag', null, $clientId);
        }

        return $flags;
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        return $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->get($this->key . ':old:' . $flag) ?: null;
        });
    }

    /**
     * 使用redis.
     *
     * @param callable $callback
     *
     * @return mixed
     */
    private function useRedis($callback)
    {
        return ImiRedis::use(function (RedisHandler $redis) use ($callback) {
            $redis->select($this->redisDb);

            return $callback($redis);
        }, $this->redisPool, true);
    }
}
