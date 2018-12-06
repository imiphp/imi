<?php
namespace Imi\Cache\Handler;

use Imi\Util\DateTime;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("RedisCache")
 */
class Redis extends Base
{
    /**
     * Redis连接池名称
     * @var string
     */
    protected $poolName;
    
    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        $this->checkKey($key);
        $result = PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($key){
            return $redis->get($key);
        });
        if(null === $result)
        {
            return $default;
        }
        else
        {
            return $this->decode($result);
        }
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)
    {
        $this->checkKey($key);
        // ttl 支持 \DateInterval 格式
        if($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        return PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($key, $value, $ttl){
            return $redis->set($key, $this->encode($value), $ttl);
        });
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        $this->checkKey($key);
        return PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($key){
            return $redis->del($key) > 0;
        });
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        return PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis){
            return $redis->flushDB();
        });
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        $this->checkArrayOrTraversable($keys);
        $mgetResult = PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($keys){
            return $redis->mget($keys);
        });
        $result = [];
        foreach($mgetResult as $i => $v)
        {
            if(null === $v)
            {
                $result[$keys[$i]] = $default;
            }
            else
            {
                $result[$keys[$i]] = $this->decode($v);
            }
        }
        return $result;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->checkArrayOrTraversable($values);
        if($values instanceof \Traversable)
        {
            $setValues = clone $values;
        }
        else
        {
            $setValues = $values;
        }
        foreach($setValues as $k => $v)
        {
            $setValues[$k] = $this->encode($v);
        }
        // ttl 支持 \DateInterval 格式
        if($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        $result = PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($setValues, $ttl){
            $result = $redis->mset($setValues);
            if(null !== $ttl)
            {
                foreach($setValues as $k => $v)
                {
                    $result = $result && $redis->expire($k, $ttl);
                }
            }
            return $result;
        });
        return $result;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        $this->checkArrayOrTraversable($keys);
        return (bool)PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($keys){
            return $redis->del($keys);
        });
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        $this->checkKey($key);
        return (bool)PoolManager::use($this->poolName, function($resource, \Imi\Redis\RedisHandler $redis) use($key){
            return $redis->exists($key);
        });
    }
}