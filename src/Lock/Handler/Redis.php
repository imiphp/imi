<?php
namespace Imi\Lock\Handler;

use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Imi\Bean\Annotation\Bean;
use Imi\Lock\Handler\BaseLock;
use Imi\Pool\PoolManager;
use Imi\Redis\RedisManager;

/**
 * Redis + Lua 实现的分布式锁，需要 Redis >= 2.6.0
 * 
 * Lua脚本来自：https://blog.csdn.net/hry2015/article/details/74937375
 * 
 * @Bean("RedisLock")
 */
class Redis extends BaseLock
{
    /**
     * Redis 连接池名称
     *
     * @var string
     */
    public $poolName;

    /**
     * Redis 几号库
     *
     * @var integer
     */
    public $db = 0;

    /**
     * 获得锁每次尝试间隔，单位：毫秒
     * 
     * @var int
     */
    public $waitSleepTime = 20;

    /**
     * Redis key
     *
     * @var string
     */
    public $key;

    /**
     * Redis key 前置
     *
     * @var string
     */
    public $keyPrefix = 'imi:lock:';

    /**
     * 当前锁对象GUID
     *
     * @var string
     */
    private $guid;

    public function __construct($id, $options = [])
    {
        parent::__construct($id, $options);
        if(null === $this->poolName)
        {
            $this->poolName = RedisManager::getDefaultPoolName();
        }
        $this->key = $this->keyPrefix . $id;
        $this->guid = md5(uniqid('', true) . spl_object_hash($this));
    }

    /**
     * 加锁，会挂起协程
     *
     * @return boolean
     */
    protected function __lock(): bool
    {
        $beginTime = microtime(true);
        do {
            if($this->__tryLock())
            {
                return true;
            }
            if(0 === $this->waitTimeout || microtime(true) - $beginTime < $this->waitTimeout / 1000)
            {
                if(Coroutine::isIn())
                {
                    Coroutine::sleep($this->waitSleepTime / 1000);
                }
                else
                {
                    usleep($this->waitSleepTime * 1000);
                }
            }
            else
            {
                break;
            }
        } while(true);
        return false;
    }

    /**
     * 尝试获取锁
     *
     * @return boolean
     */
    protected function __tryLock(): bool
    {
        return PoolManager::use($this->poolName, function($resource, $redis){
            return 1 == $redis->evalEx(<<<SCRIPT
local key     = KEYS[1]
local content = KEYS[2]
local ttl     = ARGV[2]
local db      = ARGV[1]
redis.call('select', db)
local lockSet = redis.call('setnx', key, content)
if lockSet == 1 then
redis.call('pexpire', key, ttl)
else
local value = redis.call('get', key)
if(value == content) then
lockSet = 1;
redis.call('pexpire', key, ttl)
end
end
return lockSet
SCRIPT
            , [
                $this->key,
                $this->guid,
                $this->db,
                $this->lockExpire,
            ], 2);
        });
    }

    /**
     * 解锁
     *
     * @return boolean
     */
    protected function __unlock(): bool
    {
        return PoolManager::use($this->poolName, function($resource, $redis){
            return 1 == $redis->evalEx(<<<SCRIPT
local key     = KEYS[1]
local content = KEYS[2]
local db      = ARGV[1]
redis.call('select', db)
local value = redis.call('get', key)
if value == content then
  return redis.call('del', key);
end
return 0
SCRIPT
            , [
                $this->key,
                $this->guid,
                $this->db,
            ], 2);
        });
    }

}