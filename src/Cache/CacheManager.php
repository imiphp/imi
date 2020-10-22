<?php

namespace Imi\Cache;

use Imi\Bean\BeanFactory;
use Imi\Cache\Handler\Base;
use Imi\Config;

class CacheManager
{
    /**
     * 缓存处理器数组.
     *
     * @var \Psr\SimpleCache\CacheInterface[]
     */
    protected static array $handlers = [];

    /**
     * 是否初始化.
     *
     * @var bool
     */
    protected static bool $inited = false;

    private function __construct()
    {
    }

    public static function init()
    {
        foreach (Config::getAliases() as $alias)
        {
            $caches = Config::get($alias . '.caches');
            if ($caches)
            {
                foreach ($caches as $name => $cache)
                {
                    self::addName($name, $cache['handlerClass'], $cache['option']);
                }
            }
        }
        self::$inited = true;
    }

    /**
     * 增加对象名称.
     *
     * @param string $name
     * @param string $handlerClass
     * @param array  $option
     *
     * @return void
     */
    public static function addName(string $name, string $handlerClass, array $option = [])
    {
        static::$handlers[$name] = BeanFactory::newInstance($handlerClass, $option);
    }

    /**
     * 获取所有对象名称.
     *
     * @return void
     */
    public static function getNames()
    {
        return array_keys(static::$handlers);
    }

    /**
     * 清空池子.
     *
     * @return void
     */
    public static function clearPools()
    {
        static::$handlers = [];
    }

    /**
     * 获取实例.
     *
     * @param string $name
     *
     * @return \Psr\SimpleCache\CacheInterface
     */
    public static function getInstance(string $name): Base
    {
        $handlers = &static::$handlers;
        if (!isset($handlers[$name]))
        {
            if (self::$inited)
            {
                throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
            }
            else
            {
                self::init();
                if (!isset($handlers[$name]))
                {
                    throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
                }
            }
        }

        return $handlers[$name];
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $name    对象名称
     * @param string $key     the unique key of this item in the cache
     * @param mixed  $default default value to return if the key does not exist
     *
     * @return mixed the value of the item from the cache, or $default in case of cache miss
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value
     */
    public static function get($name, $key, $default = null)
    {
        return static::getInstance($name)->get($key, $default);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $name  对象名称
     * @param string                 $key   the key of the item to store
     * @param mixed                  $value the value of the item to store, must be serializable
     * @param int|\DateInterval|null $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool true on success and false on failure
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value
     */
    public static function set($name, $key, $value, $ttl = null)
    {
        return static::getInstance($name)->set($key, $value, $ttl);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $name 对象名称
     * @param string $key  the unique cache key of the item to delete
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value
     */
    public static function delete($name, $key)
    {
        return static::getInstance($name)->delete($key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @param string $name 对象名称
     *
     * @return bool true on success and false on failure
     */
    public static function clear($name)
    {
        return static::getInstance($name)->clear();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param string   $name    对象名称
     * @param iterable $keys    a list of keys that can obtained in a single operation
     * @param mixed    $default default value to return for keys that do not exist
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if $keys is neither an array nor a Traversable,
     *                                                   or if any of the $keys are not a legal value
     */
    public static function getMultiple($name, $keys, $default = null)
    {
        return static::getInstance($name)->getMultiple($keys, $default);
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param string                 $name   对象名称
     * @param iterable               $values a list of key => value pairs for a multiple-set operation
     * @param int|\DateInterval|null $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool true on success and false on failure
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if $values is neither an array nor a Traversable,
     *                                                   or if any of the $values are not a legal value
     */
    public static function setMultiple($name, $values, $ttl = null)
    {
        return static::getInstance($name)->setMultiple($values, $ttl);
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param string   $name 对象名称
     * @param iterable $keys a list of string-based keys to be deleted
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if $keys is neither an array nor a Traversable,
     *                                                   or if any of the $keys are not a legal value
     */
    public static function deleteMultiple($name, $keys)
    {
        return static::getInstance($name)->deleteMultiple($keys);
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $name 对象名称
     * @param string $key  the cache item key
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *                                                   MUST be thrown if the $key string is not a legal value
     */
    public static function has($name, $key)
    {
        return static::getInstance($name)->has($key);
    }
}
