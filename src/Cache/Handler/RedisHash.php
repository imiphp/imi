<?php

declare(strict_types=1);

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
     */
    protected ?string $poolName = null;

    /**
     * 默认缺省的 hash key.
     */
    protected string $defaultHashKey = 'imi:RedisHashCache';

    /**
     * 分隔符，分隔 hash key和 member.
     */
    protected string $separator = '->';

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $this->parseKey($key, $member);
        $result = Redis::use(fn(\Imi\Redis\RedisHandler $redis) => $redis->hGet($key, $member), $this->poolName, true);
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
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $this->parseKey($key, $member);

        return false !== Redis::use(fn(\Imi\Redis\RedisHandler $redis) => $redis->hSet($key, $member, $this->encode($value)), $this->poolName, true);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function clear()
    {
        return (bool) Redis::use(fn(\Imi\Redis\RedisHandler $redis) => $redis->flushDB(), $this->poolName, true);
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        static $script = <<<'SCRIPT'
        local key = KEYS[1]
        local result = {}
        for i = 1, #ARGV do
            table.insert(result, redis.call('hget', key, ARGV[i]))
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

        return Redis::use(function (\Imi\Redis\RedisHandler $redis) use ($script, $keysMembers, $default, $keys) {
            $result = [];
            $i = 0;
            foreach ($keysMembers as $key => $members)
            {
                $evalResult = $redis->evalEx($script, array_merge(
                    [$key],
                    array_map([$redis, '_serialize'], $members),
                ), 1);
                foreach ($evalResult as $v)
                {
                    if (false === $v)
                    {
                        $result[$keys[$i]] = $default;
                    }
                    else
                    {
                        $result[$keys[$i]] = $this->decode($redis->_unserialize($v));
                    }
                    ++$i;
                }
            }

            return $result;
        }, $this->poolName, true);
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        static $script = <<<'SCRIPT'
        local key = KEYS[1]
        local halfLen = #ARGV / 2
        for i = 1, halfLen do
            redis.call('hset', key, ARGV[i], ARGV[halfLen + i])
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
                $result = false !== $redis->evalEx($script, array_merge([$key], array_map([$redis, '_serialize'], $item['member']), array_map([$redis, '_serialize'], $item['value'])), 1);
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
     * {@inheritDoc}
     */
    public function deleteMultiple($keys)
    {
        static $script = <<<'SCRIPT'
        local key = KEYS[1]
        for i = 1, #ARGV do
            redis.call('hdel', key, ARGV[i])
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
                    array_map([$redis, '_serialize'], $members),
                ), 1);
                if (!$result)
                {
                    return $result;
                }
            }

            return true;
        }, $this->poolName, true);
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        $this->parseKey($key, $member);

        return (bool) Redis::use(fn(\Imi\Redis\RedisHandler $redis) => $redis->hExists($key, $member), $this->poolName, true);
    }

    /**
     * 处理key.
     */
    protected function parseKey(?string &$key, ?string &$member): void
    {
        if (!\is_string($key))
        {
            throw new InvalidArgumentException('Invalid key: ' . $key);
        }
        if ('' === $this->separator)
        {
            throw new InvalidArgumentException('Invalid separator, it must be not empty');
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
