<?php

namespace Imi\Cache\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Cache\InvalidArgumentException;
use Imi\Redis\Redis;

/**
 * @Bean("RedisHashCache")
 */
class RedisHash extends Base
{
    /**
     * Redis连接池名称.
     *
     * @var string
     */
    protected $poolName;

    /**
     * 默认缺省的 hash key.
     *
     * @var string
     */
    protected $defaultHashKey = 'imi:RedisHashCache';

    /**
     * 分隔符，分隔 hash key和 member.
     *
     * @var string
     */
    protected $separator = '->';

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        $this->parseKey($key, $member);
        $result = Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($key, $member) {
            return $redis->hGet($key, $member);
        }, $this->poolName, true);
        if (false === $result)
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
     * @param int|\DateInterval|null $ttl   本驱动中无效
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)
    {
        $this->parseKey($key, $member);

        return false !== Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($key, $member, $value) {
            return $redis->hSet($key, $member, $this->encode($value));
        }, $this->poolName, true);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        $this->parseKey($key, $member);

        return Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($key, $member) {
            if (null === $member)
            {
                return $redis->del($key) > 0;
            }
            else
            {
                return $redis->hDel($key, $member) > 0;
            }
        }, $this->poolName, true);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        return (bool) Redis::use(function (\Imi\Redis\RedisHandler $redis) {
            return $redis->flushDB();
        }, $this->poolName, true);
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
     *                                                   MUST be thrown if $keys is neither an array nor a Traversable,
     *                                                   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        static $script = <<<SCRIPT
local key = KEYS[1]
local result = {}
for i = 2, #KEYS do
    table.insert(result, redis.call('hget', key, KEYS[i]))
end
return result
SCRIPT;
        $this->checkArrayOrTraversable($keys);

        $keysMembers = [];
        foreach ($keys as $key)
        {
            $this->parseKey($key, $member);
            $keysMembers[$key][] = $member;
        }

        $list = Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($script, $keysMembers) {
            $result = [];
            foreach ($keysMembers as $key => $members)
            {
                $evalResult = $redis->evalEx($script, array_merge(
                    [$key],
                    $members
                ), \count($members) + 1);
                $result = array_merge($result, $evalResult);
            }

            return $result;
        }, $this->poolName, true);
        $result = [];
        foreach ($list as $i => $v)
        {
            if (false === $v)
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
     * @param int|\DateInterval|null $ttl    本驱动中无效
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if $values is neither an array nor a Traversable,
     *                                                   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        static $script = <<<SCRIPT
local key = KEYS[1]
for i = 2, #KEYS do
    redis.call('hset', key, KEYS[i], ARGV[i - 1])
end
return true
SCRIPT;
        $this->checkArrayOrTraversable($values);

        if ($values instanceof \Traversable)
        {
            $_setValues = clone $values;
        }
        else
        {
            $_setValues = $values;
        }

        $setValues = [];
        foreach ($_setValues as $k => $v)
        {
            $this->parseKey($k, $member);
            $setValues[$k]['member'][] = $member;
            $setValues[$k]['value'][] = $this->encode($v);
        }

        $result = Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($script, $setValues) {
            foreach ($setValues as $key => $item)
            {
                $result = false !== $redis->evalEx($script, array_merge([$key], $item['member'], $item['value']), \count($item['member']) + 1);
                if (!$result)
                {
                    return $result;
                }
            }

            return true;
        }, $this->poolName, true);

        return (bool) $result;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if $keys is neither an array nor a Traversable,
     *                                                   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        static $script = <<<SCRIPT
local key = KEYS[1]
for i = 2, #KEYS do
    redis.call('hdel', key, KEYS[i])
end
return true
SCRIPT;

        $this->checkArrayOrTraversable($keys);

        $keysMembers = [];
        foreach ($keys as $key)
        {
            $this->parseKey($key, $member);
            $keysMembers[$key][] = $member;
        }

        return (bool) Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($script, $keysMembers) {
            foreach ($keysMembers as $key => $members)
            {
                $result = $redis->evalEx($script, array_merge(
                    [$key],
                    $members
                ), \count($members) + 1);
                if (!$result)
                {
                    return $result;
                }
            }

            return true;
        }, $this->poolName, true);
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
     *                                                   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        $this->parseKey($key, $member);

        return (bool) Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($key, $member) {
            return $redis->hExists($key, $member);
        }, $this->poolName, true);
    }

    /**
     * 处理key.
     *
     * @param string $key
     * @param string $member
     * @param bool   $allowNoMember 是否允许没有 $member，默认false
     *
     * @return void
     */
    protected function parseKey(&$key, &$member)
    {
        if (!\is_string($key))
        {
            throw new InvalidArgumentException('Invalid key: ' . $key);
        }
        $list = explode($this->separator, $key);

        if (isset($list[1]))
        {
            $key = $list[0];
            $member = $list[1];
        }
        else
        {
            $key = $this->defaultHashKey;
            $member = $list[0];
        }
    }
}
