<?php

namespace Imi\Server\Group\Handler;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Redis\Redis as ImiRedis;
use Imi\Redis\RedisHandler;
use Imi\ServerManage;
use Imi\Util\AtomicManager;
use Imi\Util\Swoole;
use Imi\Worker;

/**
 * @Bean("GroupRedis")
 */
class Redis implements IGroupHandler
{
    /**
     * Redis 连接池名称.
     *
     * @var string|null
     */
    protected ?string $redisPool = null;

    /**
     * redis中第几个库.
     *
     * @var int
     */
    protected int $redisDb = 0;

    /**
     * 心跳时间，单位：秒.
     *
     * @var int
     */
    protected int $heartbeatTimespan = 5;

    /**
     * 心跳数据过期时间，单位：秒.
     *
     * @var int
     */
    protected int $heartbeatTtl = 8;

    /**
     * 该服务的分组键.
     *
     * @var string
     */
    protected string $key;

    /**
     * 心跳Timer的ID.
     *
     * @var int|null
     */
    private ?int $timerId = null;

    /**
     * 组配置.
     *
     * @var array
     */
    private array $groups = [];

    /**
     * 主进程 ID.
     *
     * @var int
     */
    private int $masterPID;

    public function __init()
    {
        if (!isset($this->key))
        {
            $this->key = 'imi:' . App::getNamespace() . ':connect_group';
        }
        if (!isset($this->redisPool))
        {
            return;
        }
        $workerId = Worker::getWorkerId();
        $this->masterPID = $masterPID = Swoole::getMasterPID();
        $masterPidKey = $this->key . ':master_pid';
        if (0 === $workerId)
        {
            $this->useRedis(function (RedisHandler $redis) use ($masterPID, $masterPidKey) {
                // 判断master进程pid
                $storeMasterPID = $redis->get($masterPidKey);
                if (!$storeMasterPID)
                {
                    // 没有存储master进程pid
                    $this->initRedis($redis, $storeMasterPID);
                }
                elseif ($masterPID != $storeMasterPID)
                {
                    $hasPing = $this->hasPing($redis);
                    if ($hasPing)
                    {
                        Log::warning('Redis server group key has been used, waiting...');
                        sleep($this->heartbeatTtl);
                        $hasPing = $this->hasPing($redis);
                    }
                    if ($hasPing)
                    {
                        // 与master进程ID不等
                        Log::emergency('Redis server group key has been used');
                        ServerManage::getServer('main')->getSwooleServer()->shutdown();
                    }
                    else
                    {
                        $this->initRedis($redis, $storeMasterPID);
                        Log::info('Redis server group key init');
                    }
                }
                $this->startPing($redis);
                AtomicManager::wakeup('imi.GroupRedisLock', Worker::getWorkerNum());
            });
        }
        elseif ($workerId > 0)
        {
            if (!$this->useRedis(function (RedisHandler $redis) use ($masterPID, $masterPidKey) {
                return $masterPID == $redis->get($masterPidKey);
            }))
            {
                AtomicManager::wait('imi.GroupRedisLock');
            }
        }
    }

    /**
     * 初始化redis数据.
     *
     * @param RedisHandler $redis
     * @param int|null     $storeMasterPID
     *
     * @return void
     */
    private function initRedis(RedisHandler $redis, ?int $storeMasterPID = null)
    {
        if (null !== $storeMasterPID)
        {
            $redis->del($this->key . ':master_pid');
        }
        if ($redis->setnx($this->key . ':master_pid', $this->masterPID))
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
     *
     * @return string
     */
    private function getGroupsKey(): string
    {
        return $this->key . ':groups';
    }

    /**
     * 开始ping.
     *
     * @param RedisHandler $redis
     *
     * @return void
     */
    private function startPing(RedisHandler $redis)
    {
        if ($this->ping($redis))
        {
            // 心跳定时器
            $this->timerId = \Swoole\Timer::tick($this->heartbeatTimespan * 1000, [$this, 'pingTimer']);
            Event::on('IMI.MAIN_SERVER.WORKER.EXIT', function () {
                \Swoole\Timer::clear($this->timerId);
                $this->timerId = null;
            }, \Imi\Util\ImiPriority::IMI_MIN);
        }
    }

    /**
     * ping定时器执行操作.
     *
     * @return void
     */
    public function pingTimer()
    {
        $this->useRedis(function ($redis) {
            $this->ping($redis);
        });
    }

    /**
     * 获取redis中存储ping的key.
     *
     * @return string
     */
    private function getPingKey(): string
    {
        return $this->key . ':ping';
    }

    /**
     * ping操作.
     *
     * @param RedisHandler $redis
     *
     * @return bool
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

    /**
     * 是否有ping.
     *
     * @param RedisHandler $redis
     *
     * @return bool
     */
    private function hasPing(RedisHandler $redis): bool
    {
        $key = $this->getPingKey();

        return 1 == $redis->exists($key);
    }

    public function __destruct()
    {
        if (null !== $this->timerId)
        {
            \Swoole\Timer::clear($this->timerId);
        }
    }

    /**
     * 组是否存在.
     *
     * @param string $groupName
     *
     * @return bool
     */
    public function hasGroup(string $groupName): bool
    {
        return true;
    }

    /**
     * 创建组，返回组对象
     *
     * @param string $groupName
     * @param int    $maxClients
     *
     * @return void
     */
    public function createGroup(string $groupName, int $maxClients = -1)
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
     *
     * @param string $groupName
     *
     * @return void
     */
    public function closeGroup(string $groupName)
    {
        $this->useRedis(function (RedisHandler $redis) use ($groupName) {
            $key = $this->getGroupNameKey($groupName);
            $redis->del($key);
            $redis->sRem($this->getGroupsKey(), $groupName);
        });
    }

    /**
     * 加入组，组不存在则自动创建.
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return void
     */
    public function joinGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $fd) {
            $key = $this->getGroupNameKey($groupName);

            return $redis->sadd($key, $fd) > 0;
        });
    }

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return void
     */
    public function leaveGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $fd) {
            $key = $this->getGroupNameKey($groupName);

            return $redis->srem($key, $fd) > 0;
        });
    }

    /**
     * 连接是否存在于组里.
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return bool
     */
    public function isInGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $fd) {
            $key = $this->getGroupNameKey($groupName);
            $redis->sIsMember($key, $fd);
        });
    }

    /**
     * 获取所有fd.
     *
     * @param string $groupName
     *
     * @return int[]
     */
    public function getFds(string $groupName): array
    {
        $groups = $this->groups;

        return $this->useRedis(function (RedisHandler $redis) use ($groupName, $groups) {
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
    }

    /**
     * 获取组名处理后的键名.
     *
     * @param string $groupName
     *
     * @return string
     */
    public function getGroupNameKey(string $groupName): string
    {
        return $this->key . ':groups:' . $groupName;
    }

    /**
     * 获取组中的连接总数.
     *
     * @return int
     */
    public function count(string $groupName): int
    {
        return $this->useRedis(function (RedisHandler $redis) use ($groupName) {
            $key = $this->getGroupNameKey($groupName);

            return $redis->scard($key);
        });
    }

    /**
     * 清空分组.
     *
     * @return void
     */
    public function clear()
    {
        return $this->useRedis(function (RedisHandler $redis) {
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
