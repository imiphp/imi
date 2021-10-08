<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Lock;
use Imi\Redis\Redis;
use Imi\Redis\RedisHandler;
use Imi\Server\ConnectionContext\StoreHandler\IHandler;
use Imi\Swoole\Util\MemoryTableManager;
use Imi\Worker;
use Swoole\Timer;

/**
 * 连接上下文存储处理器-MemoryTable.
 *
 * @Bean(name="ConnectionContextMemoryTable", env="swoole")
 */
class MemoryTable implements IHandler
{
    /**
     * 数据写入前编码回调.
     *
     * @var callable|null
     */
    protected $dataEncode = 'serialize';

    /**
     * 数据读出后处理回调.
     *
     * @var callable|null
     */
    protected $dataDecode = 'unserialize';

    /**
     * 表名.
     */
    protected string $tableName = '';

    /**
     * Redis 连接池名称.
     */
    protected ?string $redisPool = null;

    /**
     * redis中第几个库.
     */
    protected ?int $redisDb = null;

    /**
     * 键.
     */
    protected string $key = 'imi:connectionBinder:map';

    /**
     * 锁 ID.
     */
    protected ?string $lockId = null;

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
     * 读取数据.
     */
    public function read(string $key): array
    {
        $result = MemoryTableManager::get($this->tableName, $key, 'data');
        if ($result)
        {
            if ($this->dataDecode)
            {
                return ($this->dataDecode)($result);
            }
            else
            {
                return $result;
            }
        }
        else
        {
            return [];
        }
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        if ($this->dataEncode)
        {
            $data = ($this->dataEncode)($data);
        }
        MemoryTableManager::set($this->tableName, $key, ['data' => $data]);
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        MemoryTableManager::del($this->tableName, $key);
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return MemoryTableManager::exist($this->tableName, $key);
    }

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        if ($this->lockId)
        {
            return Lock::getInstance($this->lockId, $key)->lock($callable);
        }
        else
        {
            return MemoryTableManager::lock($this->tableName, $callable);
        }
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        if ($this->lockId)
        {
            return Lock::unlock($this->lockId);
        }
        else
        {
            return MemoryTableManager::unlock($this->tableName);
        }
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        $this->lock((string) $clientId, function () use ($flag, $clientId) {
            $data = $this->read((string) $clientId);
            $data['__flag'] = $flag;
            $this->save((string) $clientId, $data);
        });
        $this->useRedis(function (RedisHandler $redis) use ($flag, $clientId) {
            $redis->hSet($this->key . ':binder', $flag, $clientId);
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
            return $redis->hSetNx($this->key . ':binder', $flag, $clientId);
        });
        if ($result)
        {
            $this->lock((string) $clientId, function () use ($flag, $clientId) {
                $data = $this->read((string) $clientId);
                $data['__flag'] = $flag;
                $this->save((string) $clientId, $data);
            });
        }

        return $result;
    }

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($flag, $clientId, $keepTime) {
            $key = $this->key . ':binder';
            $this->lock((string) $clientId, function () use ($flag, $clientId) {
                $data = $this->read((string) $clientId);
                $data['__flag'] = $flag;
                $this->save((string) $clientId, $data);
            });
            $redis->multi();
            $redis->hDel($key, $flag);
            if ($keepTime > 0)
            {
                $redis->set($key . ':old:' . $flag, $clientId, $keepTime);
            }
            $redis->exec();
        });
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getClientIdByFlag(string $flag): array
    {
        return (array) $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->hGet($this->key . ':binder', $flag) ?: null;
        });
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public function getClientIdsByFlags(array $flags): array
    {
        $result = $this->useRedis(function (RedisHandler $redis) use ($flags) {
            return $redis->hMget($this->key . ':binder', $flags);
        });
        foreach ($result as $k => $v)
        {
            $result[$k] = [$v];
        }

        return $result;
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string
    {
        return $this->read((string) $clientId)['__flag'] ?? null;
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
            $flags[$clientId] = $this->read((string) $clientId)['__flag'] ?? null;
        }

        return $flags;
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        return $this->useRedis(function (RedisHandler $redis) use ($flag) {
            return $redis->get($this->key . ':binder:old:' . $flag) ?: null;
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
        return Redis::use(function (RedisHandler $redis) use ($callback) {
            if (null !== $this->redisDb)
            {
                $redis->select($this->redisDb);
            }

            return $callback($redis);
        }, $this->redisPool, true);
    }
}
