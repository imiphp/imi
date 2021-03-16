<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\StoreHandler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Lock\Lock;
use Imi\Redis\Redis as ImiRedis;
use Imi\Redis\RedisHandler;
use Imi\Timer\Timer;
use Imi\Worker;

/**
 * 连接上下文存储处理器-Redis.
 *
 * @Bean("ConnectContextRedis")
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
    protected string $key = '';

    /**
     * 心跳时间，单位：秒.
     */
    protected int $heartbeatTimespan = 5;

    /**
     * 心跳数据过期时间，单位：秒.
     */
    protected int $heartbeatTtl = 8;

    /**
     * 数据写入前编码回调.
     *
     * @var callable|null
     */
    protected $dataEncode = null;

    /**
     * 数据读出后处理回调.
     *
     * @var callable|null
     */
    protected $dataDecode = null;

    /**
     * 锁 ID.
     */
    protected ?string $lockId = null;

    /**
     * 心跳Timer的ID.
     */
    private ?int $timerId = null;

    /**
     * 主进程 ID.
     */
    private int $masterPID = 0;

    public function __init(): void
    {
        if ('' === $this->key)
        {
            $this->key = 'imi:' . App::getNamespace() . ':connect_context';
        }
        if (null === $this->redisPool)
        {
            return;
        }
        if (!$this->lockId)
        {
            throw new \RuntimeException('ConnectContextRedis lockId must be set');
        }
        $workerId = Worker::getWorkerId();
        $this->masterPID = $masterPID = Worker::getMasterPid();

        if (0 === $workerId)
        {
            $this->useRedis(function (RedisHandler $redis) use ($masterPID) {
                $this->initRedis($redis, $masterPID);
                $this->startPing($redis);
            });
        }
    }

    /**
     * 初始化redis数据.
     */
    private function initRedis(RedisHandler $redis, ?int $storeMasterPID = null): void
    {
        if (null !== $storeMasterPID)
        {
            $redis->del($this->key . ':master_pid');
        }
        if ($redis->setnx($this->key . ':master_pid', $this->masterPID))
        {
            // 清空存储列表
            $redis->del($this->getStoreKey());
        }
    }

    /**
     * 开始ping.
     */
    private function startPing(RedisHandler $redis): void
    {
        if ($this->ping($redis))
        {
            // 心跳定时器
            $this->timerId = Timer::tick($this->heartbeatTimespan * 1000, [$this, 'pingTimer']);
            Event::on('IMI.MAIN_SERVER.WORKER.EXIT', function () {
                Timer::del($this->timerId);
                $this->timerId = null;
            }, \Imi\Util\ImiPriority::IMI_MIN);
        }
    }

    /**
     * ping定时器执行操作.
     */
    public function pingTimer(): void
    {
        $this->useRedis(function (RedisHandler $redis) {
            $this->ping($redis);
        });
    }

    /**
     * 获取redis中存储ping的key.
     */
    private function getPingKey(): string
    {
        return $this->key . ':ping';
    }

    /**
     * ping操作.
     */
    private function ping(RedisHandler $redis): bool
    {
        $key = $this->getPingKey();
        $redis->multi();
        $redis->set($key, '');
        $redis->expire($key, $this->heartbeatTtl);
        $result = $redis->exec();
        if (!$result)
        {
            return false;
        }
        foreach ($result as $value)
        {
            if (!$value)
            {
                return false;
            }
        }

        return true;
    }

    public function __destruct()
    {
        if (null !== $this->timerId)
        {
            Timer::del($this->timerId);
        }
    }

    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        return $this->useRedis(function (RedisHandler $redis) use ($key) {
            $result = $redis->hGet($this->getStoreKey(), $key);
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
        });
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($key, $data) {
            if ($this->dataEncode)
            {
                $data = ($this->dataEncode)($data);
            }
            $redis->hSet($this->getStoreKey(), $key, $data);
        });
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($key) {
            $redis->hDel($this->getStoreKey(), $key);
        });
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($ttl) {
            $redis->expire($this->getStoreKey(), $ttl);
        });
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($key) {
            return $redis->hExists($this->getStoreKey(), $key);
        });
    }

    /**
     * 获取存储hash键名.
     */
    private function getStoreKey(): string
    {
        return $this->key . ':store';
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

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        return Lock::getInstance($this->lockId, $key)->lock($callable);
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        return Lock::unlock($this->lockId);
    }
}
