<?php

declare(strict_types=1);

namespace Imi\Server\Group\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Redis\Redis as ImiRedis;
use Imi\Redis\RedisHandler;
use Imi\Timer\Timer;
use Imi\Worker;

/**
 * @Bean("GroupRedis")
 */
class Redis implements IGroupHandler
{
    /**
     * Redis 连接池名称.
     */
    protected ?string $redisPool = null;

    /**
     * redis中第几个库.
     */
    protected ?int $redisDb = null;

    /**
     * 心跳时间，单位：秒.
     */
    protected int $heartbeatTimespan = 5;

    /**
     * 心跳数据过期时间，单位：秒.
     */
    protected int $heartbeatTtl = 8;

    /**
     * 该服务的分组键.
     */
    protected string $key = '';

    /**
     * 心跳Timer的ID.
     */
    private ?int $timerId = null;

    /**
     * 组配置.
     */
    private array $groups = [];

    /**
     * 主进程 ID.
     */
    private int $masterPID = 0;

    /**
     * 启动时执行.
     */
    public function startup(): void
    {
        $this->clear();
    }

    public function __init(): void
    {
        if ('' === $this->key)
        {
            $this->key = 'imi:' . App::getNamespace() . ':connect_group';
        }
        if (null === $this->redisPool)
        {
            return;
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
        if ($r = $redis->setnx($this->key . ':master_pid', $this->masterPID))
        {
            // 清空分组列表
            $groupsKey = $this->getGroupsKey();

            $it = null;
            while (false !== ($list = $redis->sScan($groupsKey, $it, '*', 1000)))
            {
                foreach ($list as $groupName)
                {
                    $redis->del($this->getGroupNameKey($groupName));
                }
                if (0 == $it)
                {
                    break;
                }
            }

            $redis->del($groupsKey);
        }
    }

    /**
     * 获取存放组名的set键.
     */
    private function getGroupsKey(): string
    {
        return $this->key . ':groups';
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
                if ($this->timerId)
                {
                    Timer::del($this->timerId);
                    $this->timerId = null;
                }
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
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool
    {
        return true;
    }

    /**
     * 创建组，返回组对象
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $this->useRedis(function (RedisHandler $redis) use ($groupName) {
                $redis->sAdd($this->getGroupsKey(), $groupName);
            });
            $groups[$groupName] = [
                'maxClient' => $maxClients,
            ];
        }
    }

    /**
     * 关闭组.
     */
    public function closeGroup(string $groupName): void
    {
        $this->useRedis(function (RedisHandler $redis) use ($groupName) {
            $key = $this->getGroupNameKey($groupName);
            $redis->del($key);
            $redis->srem($this->getGroupsKey(), $groupName);
        });
    }

    /**
     * 加入组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function joinGroup(string $groupName, $clientId): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $clientId): bool {
            $key = $this->getGroupNameKey($groupName);

            return $redis->sAdd($key, $clientId) > 0;
        });
    }

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function leaveGroup(string $groupName, $clientId): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $clientId): bool {
            $key = $this->getGroupNameKey($groupName);

            return $redis->srem($key, $clientId) > 0;
        });
    }

    /**
     * 连接是否存在于组里.
     *
     * @param int|string $clientId
     */
    public function isInGroup(string $groupName, $clientId): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $clientId): bool {
            $key = $this->getGroupNameKey($groupName);

            return $redis->sismember($key, $clientId);
        });
    }

    /**
     * 获取所有连接ID.
     *
     * @return int[]|string[]
     */
    public function getClientIds(string $groupName): array
    {
        $groups = $this->groups;

        $result = $this->useRedis(function (RedisHandler $redis) use ($groupName, $groups): array {
            $key = $this->getGroupNameKey($groupName);
            if ($groups[$groupName]['maxClient'] > 0)
            {
                return $redis->sRandMember($key, $groups[$groupName]['maxClient']);
            }
            else
            {
                return $redis->sMembers($key);
            }
        });

        foreach ($result as &$value)
        {
            $value = (int) $value;
        }

        return $result;
    }

    /**
     * 获取组名处理后的键名.
     */
    public function getGroupNameKey(string $groupName): string
    {
        return $this->key . ':groups:' . $groupName;
    }

    /**
     * 获取组中的连接总数.
     */
    public function count(string $groupName): int
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName): int {
            $key = $this->getGroupNameKey($groupName);

            return $redis->scard($key);
        });
    }

    /**
     * 清空分组.
     */
    public function clear(): void
    {
        $this->useRedis(function (RedisHandler $redis) {
            $keys = [];
            $count = 0;
            foreach ($redis->scanEach($this->getGroupNameKey('*')) as $key)
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

    /**
     * 使用redis.
     *
     * @return mixed
     */
    private function useRedis(callable $callback)
    {
        return ImiRedis::use(function (RedisHandler $redis) use ($callback) {
            if (null !== $this->redisDb)
            {
                $redis->select($this->redisDb);
            }

            return $callback($redis);
        }, $this->redisPool, true);
    }
}
