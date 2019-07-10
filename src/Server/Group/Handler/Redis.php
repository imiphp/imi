<?php
namespace Imi\Server\Group\Handler;

use Imi\Worker;
use Imi\Log\Log;
use Imi\Event\Event;
use Imi\Util\Swoole;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Util\ArrayUtil;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("GroupRedis")
 */
class Redis implements IGroupHandler
{
    /**
     * Redis 连接池名称
     *
     * @var string
     */
    protected $redisPool;

    /**
     * redis中第几个库
     *
     * @var integer
     */
    protected $redisDb = 0;

    /**
     * 心跳时间，单位：秒
     *
     * @var int
     */
    protected $heartbeatTimespan = 5;

    /**
     * 心跳数据过期时间，单位：秒
     *
     * @var int
     */
    protected $heartbeatTtl = 8;

    /**
     * 该服务的分组键
     * 
     * @var string
     */
    protected $key = 'imi:connect_group';

    /**
     * 心跳Timer的ID
     *
     * @var int
     */
    private $timerID;

    /**
     * 组配置
     *
     * @var array
     */
    private $groups = [];

    /**
     * 主进程 ID
     * @var int
     */
    private $masterPID;

    public function __init()
    {
        if(null === $this->redisPool)
        {
            return;
        }
        if(0 === Worker::getWorkerID())
        {
            $this->useRedis(function($resource, $redis){
                // 判断master进程pid
                $this->masterPID = Swoole::getMasterPID();
                $storeMasterPID = $redis->get($this->key . ':master_pid');
                if(!$storeMasterPID)
                {
                    // 没有存储master进程pid
                    $this->initRedis($redis, $storeMasterPID);
                }
                else if($this->masterPID != $storeMasterPID)
                {
                    $hasPing = $this->hasPing($redis);
                    if($hasPing)
                    {
                        Log::warning('Redis server group key has been used, waiting...');
                        sleep($this->heartbeatTtl);
                        $hasPing = $this->hasPing($redis);
                    }
                    if($hasPing)
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
            });
        }
    }

    /**
     * 初始化redis数据
     *
     * @param mixed $redis
     * @param int $storeMasterPID
     * @return void
     */
    private function initRedis($redis, $storeMasterPID = null)
    {
        if(null !== $storeMasterPID)
        {
            $redis->del($this->key . ':master_pid');
        }
        if($redis->setnx($this->key. ':master_pid', $this->masterPID))
        {
            // 清空分组列表
            $groupsKey = $this->getGroupsKey();

            $it = null;
            while(false !== ($list = $redis->sScan($groupsKey, $it, '*', 1000)))
            {
                foreach($list as $groupName)
                {
                    $redis->del($this->getGroupNameKey($groupName));
                }
                if(0 == $it)
                {
                    break;
                }
            }

            $redis->del($groupsKey);
        }
    }

    /**
     * 获取存放组名的set键
     *
     * @return string
     */
    private function getGroupsKey(): string
    {
        return $this->key . ':groups';
    }

    /**
     * 开始ping
     *
     * @param mixed $redis
     * @return void
     */
    private function startPing($redis)
    {
        if($this->ping($redis))
        {
            // 心跳定时器
            $this->timerID = \Swoole\Timer::tick($this->heartbeatTimespan * 1000, [$this, 'pingTimer']);
            Event::on('IMI.MAIN_SERVER.WORKER.EXIT', function(){
                \Swoole\Timer::clear($this->timerID);
                $this->timerID = null;
            }, \Imi\Util\ImiPriority::IMI_MIN);
        }
    }

    /**
     * ping定时器执行操作
     *
     * @return void
     */
    public function pingTimer()
    {
        $this->useRedis(function($resource, $redis){
            $this->ping($redis);
        });
    }

    /**
     * 获取redis中存储ping的key
     *
     * @return void
     */
    private function getPingKey()
    {
        return $this->key . ':ping';
    }

    /**
     * ping操作
     *
     * @param mixed $redis
     * @return boolean
     */
    private function ping($redis)
    {
        $key = $this->getPingKey();
        $redis->multi();
        $redis->set($key, '');
        $redis->expire($key, $this->heartbeatTtl);
        $result = $redis->exec();
        if(!$result)
        {
            return false;
        }
        foreach($result as $value)
        {
            if(!$value)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 是否有ping
     *
     * @param mixed $redis
     * @return boolean
     */
    private function hasPing($redis)
    {
        $key = $this->getPingKey();
        return 1 == $redis->exists($key);
    }

    public function __destruct()
    {
        if(null !== $this->timerID)
        {
            \Swoole\Timer::clear($this->timerID);
        }
    }

    /**
     * 组是否存在
     *
     * @param string $groupName
     * @return boolean
     */
    public function hasGroup(string $groupName)
    {
        return true;
    }

    /**
     * 创建组，返回组对象
     *
     * @param string $groupName
     * @param integer $maxClients
     * @return void
     */
    public function createGroup(string $groupName, int $maxClients = -1)
    {
        if(!isset($this->groups[$groupName]))
        {
            $this->useRedis(function($resource, $redis) use($groupName){
                $redis->sAdd($this->getGroupsKey(), $groupName);
            });
            $this->groups[$groupName] = [
                'maxClient' => $maxClients,
            ];
        }
    }

    /**
     * 关闭组
     *
     * @param string $groupName
     * @return void
     */
    public function closeGroup(string $groupName)
    {
        $this->useRedis(function($resource, $redis) use($groupName){
            $key = $this->getGroupNameKey($groupName);
            try{
                $redis->del($key);
                $redis->sRem($this->getGroupsKey(), $groupName);
            }
            catch(\Throwable $ex)
            {

            }
        });
    }

    /**
     * 加入组，组不存在则自动创建
     *
     * @param string $groupName
     * @param integer $fd
     * @return void
     */
    public function joinGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function($resource, $redis) use($groupName, $fd){
            $key = $this->getGroupNameKey($groupName);
            return $redis->sadd($key, $fd) > 0;
        });
    }

    /**
     * 离开组，组不存在则自动创建
     *
     * @param string $groupName
     * @param integer $fd
     * @return void
     */
    public function leaveGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function($resource, $redis) use($groupName, $fd){
            $key = $this->getGroupNameKey($groupName);
            return $redis->srem($key, $fd) > 0;
        });
    }

    /**
     * 连接是否存在于组里
     *
     * @param string $groupName
     * @param integer $fd
     * @return boolean
     */
    public function isInGroup(string $groupName, int $fd): bool
    {
        return $this->useRedis(function($resource, $redis) use($groupName, $fd){
            $key = $this->getGroupNameKey($groupName);
            $redis->sIsMember($key, $fd);
        });
    }

    /**
     * 获取所有fd
     *
     * @param string $groupName
     * @return int[]
     */
    public function getFds(string $groupName): array
    {
        return $this->useRedis(function($resource, $redis) use($groupName){
            $key = $this->getGroupNameKey($groupName);
            if($this->groups[$groupName]['maxClient'] > 0)
            {
                return $redis->sRandMember($key, $this->groups[$groupName]['maxClient']);
            }
            else
            {
                return $redis->sMembers($key);
            }
        });
    }

    /**
     * 获取组名处理后的键名
     *
     * @param string $groupName
     * @return string
     */
    public function getGroupNameKey(string $groupName): string
    {
        return $this->key . ':groups:' . $groupName;
    }

    /**
     * 获取组中的连接总数
     * @return integer
     */
    public function count(string $groupName): int
    {
        return $this->useRedis(function($resource, $redis) use($groupName){
            $key = $this->getGroupNameKey($groupName);
            return $redis->scard($key);
        });
    }

    /**
     * 使用redis
     *
     * @param callable $callback
     * @return void
     */
    private function useRedis($callback)
    {
        return PoolManager::use($this->redisPool, function($resource, $redis) use($callback){
            $redis->select($this->redisDb);
            return $callback($resource, $redis);
        });
    }
}