<?php

declare(strict_types=1);

namespace Imi\RateLimit;

use Imi\Pool\PoolManager;
use Imi\Redis\RedisHandler;
use Imi\Redis\RedisManager;

abstract class WorkerLimiterLock
{
    /**
     * @return mixed
     */
    public static function lock(string $name, int $max, ?float $timeout, ?string $poolName = null)
    {
        $now = microtime(true);
        $args = [
            '{' . $name . '}',     // 名称
            $max,      // 最大允许数量
            $now,      // 当前时间
            null === $timeout ? null : ($now + $timeout), // 过期时间
        ];
        $numKeys = 1;

        return PoolManager::use($poolName ?? RedisManager::getDefaultPoolName(), function ($resource, RedisHandler $redis) use ($args, $numKeys) {
            $redis->clearLastError();
            $result = $redis->evalEx(<<<'SCRIPT'
local name = KEYS[1]
local max = ARGV[1]
local now = ARGV[2]
local timeout = ARGV[3]
local timeoutKey = name .. ':timeout'
-- 处理超时
local kv = redis.call('ZRANGEBYSCORE', timeoutKey, '-inf', now)
for key, value in pairs(kv) do
    redis.call('decr', name)
    redis.call('zrem', timeoutKey, value)
end
local num = redis.call('get', name)
if(num ~= false and num < '0')
then
    redis.call('del', name)
end
-- 判断值是否超过最大允许数量
local num = redis.call('get', name)
if(num ~= false and num >= max)
then
    return false
end
-- 写入超时列表
local id = redis.call('incr', name .. ':id_incr')
if(timeout ~= nil and false == redis.call('zadd', timeoutKey, timeout, id))
then
    return false
end
-- 累加
if(false == redis.call('incr', name))
then
    return false
end
return id
SCRIPT
            , $args, $numKeys);
            if (!$result && '' !== ($error = $redis->getLastError()))
            {
                throw new \RuntimeException($error);
            }

            return $result;
        });
    }

    /**
     * @return mixed
     */
    public static function unlock(string $name, int $workerId, ?string $poolName = null)
    {
        $args = [
            '{' . $name . '}',     // 名称
            $workerId, // 任务ID
        ];
        $numKeys = 1;

        return PoolManager::use($poolName ?? RedisManager::getDefaultPoolName(), function ($resource, RedisHandler $redis) use ($args, $numKeys) {
            $redis->clearLastError();
            $result = $redis->evalEx(<<<'SCRIPT'
local name = KEYS[1]
local id = ARGV[1]
local timeoutKey = name .. ':timeout'
local num = redis.call('get', name)
redis.call('zrem', timeoutKey, id)
if(num ~= false and num > '0')
then
    return redis.call('decr', name) >= 0
end
return false
SCRIPT
, $args, $numKeys);
            if (!$result && '' !== ($error = $redis->getLastError()))
            {
                throw new \RuntimeException($error);
            }

            return $result;
        });
    }
}
