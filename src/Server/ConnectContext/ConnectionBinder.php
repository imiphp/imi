<?php
namespace Imi\Server\ConnectContext;

use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;
use Imi\Redis\RedisHandler;

/**
 * 连接绑定器
 * @Bean("ConnectionBinder")
 */
class ConnectionBinder
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
     * 键
     * 
     * @var string
     */
    protected $key = 'imi:connectionBinder:map';

    /**
     * 绑定一个标记到当前连接
     *
     * @param string $flag
     * @param integer $fd
     * @return void
     */
    public function bind(string $flag, int $fd)
    {
        $this->useRedis(function(RedisHandler $redis) use($flag, $fd){
            $redis->hSet($this->key, $flag, $fd);
        });
    }

    /**
     * 取消绑定
     *
     * @param string $flag
     * @return void
     */
    public function unbind(string $flag)
    {
        $this->useRedis(function(RedisHandler $redis) use($flag){
            $redis->hDel($this->key, $flag);
        });
    }

    /**
     * 使用标记获取连接编号
     *
     * @param string $flag
     * @return int|null
     */
    public function getFdByFlag(string $flag): ?int
    {
        return $this->useRedis(function(RedisHandler $redis) use($flag){
            return $redis->hGet($this->key, $flag);
        });
    }

    /**
     * 使用redis
     *
     * @param callable $callback
     * @return mixed
     */
    private function useRedis($callback)
    {
        return PoolManager::use($this->redisPool, function($resource, $redis) use($callback){
            $redis->select($this->redisDb);
            return $callback($redis);
        });
    }

}
