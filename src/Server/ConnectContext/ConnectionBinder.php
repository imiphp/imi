<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\Redis\Redis;
use Imi\Redis\RedisHandler;
use Imi\Worker;

/**
 * 连接绑定器.
 *
 * @Bean("ConnectionBinder")
 */
class ConnectionBinder
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

    public function __init(): void
    {
        if (0 === Worker::getWorkerId())
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
     */
    public function bind(string $flag, int $fd): void
    {
        ConnectContext::set('__flag', $flag, $fd);
        $this->useRedis(function (RedisHandler $redis) use ($flag, $fd) {
            $redis->hSet($this->key, $flag, $fd);
        });
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     */
    public function bindNx(string $flag, int $fd): bool
    {
        $result = $this->useRedis(function (RedisHandler $redis) use ($flag, $fd) {
            return $redis->hSetNx($this->key, $flag, $fd);
        });
        if ($result)
        {
            ConnectContext::set('__flag', $flag, $fd);
        }

        return $result;
    }

    /**
     * 取消绑定.
     *
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, int $keepTime = null): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($flag, $keepTime) {
            $key = $this->key;
            if ($fd = $redis->hGet($key, $flag))
            {
                ConnectContext::set('__flag', null, (int) $fd);
            }
            $redis->multi();
            $redis->hDel($key, $flag);
            if ($fd && $keepTime > 0)
            {
                $redis->set($key . ':old:' . $flag, $fd, $keepTime);
            }
            $redis->exec();
        });
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getFdByFlag(string $flag): ?int
    {
        $result = $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->hGet($this->key, $flag);
        });
        if (false === $result)
        {
            return null;
        }
        else
        {
            return (int) $result;
        }
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]
     */
    public function getFdsByFlags(array $flags): array
    {
        return $this->useRedis(function (RedisHandler $redis) use ($flags) {
            return $redis->hMget($this->key, $flags);
        });
    }

    /**
     * 使用连接编号获取标记.
     */
    public function getFlagByFd(int $fd): ?string
    {
        return ConnectContext::get('__flag', null, $fd);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[] $fds
     *
     * @return string[]
     */
    public function getFlagsByFds(array $fds): array
    {
        $flags = [];
        foreach ($fds as $fd)
        {
            $flags[$fd] = ConnectContext::get('__flag', null, $fd);
        }

        return $flags;
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldFdByFlag(string $flag): ?int
    {
        $result = $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->get($this->key . ':old:' . $flag);
        });
        if (false === $result)
        {
            return null;
        }
        else
        {
            return (int) $result;
        }
    }

    /**
     * 使用redis.
     *
     * @return mixed
     */
    private function useRedis(callable $callback)
    {
        return Redis::use(function (RedisHandler $redis) use ($callback) {
            $redis->select($this->redisDb);

            return $callback($redis);
        }, $this->redisPool, true);
    }
}
