<?php

declare(strict_types=1);

namespace Imi\Redis;

use RedisException;

/**
 * imi 框架中封装的 Redis 类.
 *
 * @method mixed _prefix($key)
 * @method mixed _serialize($value)
 * @method mixed _unserialize($value)
 * @method mixed append($key, $value)
 * @method mixed auth($password)
 * @method mixed bgSave()
 * @method mixed bgrewriteaof()
 * @method mixed bitcount($key)
 * @method mixed bitop($operation, $ret_key, $key, ...$other_keys)
 * @method mixed bitpos($key, $bit, $start = null, $end = null)
 * @method mixed blPop($key, $timeout_or_key, ...$extra_args)
 * @method mixed brPop($key, $timeout_or_key, ...$extra_args)
 * @method mixed brpoplpush($src, $dst, $timeout)
 * @method mixed bzPopMax($key, $timeout_or_key, ...$extra_args)
 * @method mixed bzPopMin($key, $timeout_or_key, ...$extra_args)
 * @method mixed clearLastError()
 * @method mixed client($cmd, ...$args)
 * @method mixed close()
 * @method mixed command(...$args)
 * @method mixed config($cmd, $key, $value = null)
 * @method mixed connect($host, $port = null, $timeout = null, $retry_interval = null)
 * @method mixed dbSize()
 * @method mixed debug($key)
 * @method mixed decr($key)
 * @method mixed decrBy($key, $value)
 * @method mixed del($key, ...$other_keys)
 * @method mixed discard()
 * @method mixed dump($key)
 * @method mixed echo($msg)
 * @method mixed eval($script, $args = null, $num_keys = null)
 * @method mixed evalsha($script_sha, $args = null, $num_keys = null)
 * @method mixed exec()
 * @method mixed exists($key, ...$other_keys)
 * @method mixed expire($key, $timeout)
 * @method mixed expireAt($key, $timestamp)
 * @method mixed flushAll($async = null)
 * @method mixed flushDB($async = null)
 * @method mixed geoadd($key, $lng, $lat, $member, ...$other_triples)
 * @method mixed geodist($key, $src, $dst, $unit = null)
 * @method mixed geohash($key, $member, ...$other_members)
 * @method mixed geopos($key, $member, ...$other_members)
 * @method mixed georadius($key, $lng, $lan, $radius, $unit, array $opts = null)
 * @method mixed georadius_ro($key, $lng, $lan, $radius, $unit, array $opts = null)
 * @method mixed georadiusbymember($key, $member, $radius, $unit, array $opts = null)
 * @method mixed georadiusbymember_ro($key, $member, $radius, $unit, array $opts = null)
 * @method mixed get($key)
 * @method mixed getAuth()
 * @method mixed getBit($key, $offset)
 * @method mixed getDBNum()
 * @method mixed getHost()
 * @method mixed getLastError()
 * @method mixed getMode()
 * @method mixed getOption($option)
 * @method mixed getPersistentId()
 * @method mixed getPort()
 * @method mixed getRange($key, $start, $end)
 * @method mixed getReadTimeout()
 * @method mixed getSet($key, $value)
 * @method mixed getTimeout()
 * @method mixed hDel($key, $member, ...$other_members)
 * @method mixed hExists($key, $member)
 * @method mixed hGet($key, $member)
 * @method mixed hGetAll($key)
 * @method mixed hIncrBy($key, $member, $value)
 * @method mixed hIncrByFloat($key, $member, $value)
 * @method mixed hKeys($key)
 * @method mixed hLen($key)
 * @method mixed hMget($key, array $keys)
 * @method mixed hMset($key, array $pairs)
 * @method mixed hSet($key, $member, $value)
 * @method mixed hSetNx($key, $member, $value)
 * @method mixed hStrLen($key, $member)
 * @method mixed hVals($key)
 * @method mixed incr($key)
 * @method mixed incrBy($key, $value)
 * @method mixed incrByFloat($key, $value)
 * @method mixed info($option = null)
 * @method mixed isConnected()
 * @method mixed keys($pattern)
 * @method mixed lInsert($key, $position, $pivot, $value)
 * @method mixed lLen($key)
 * @method mixed lPop($key)
 * @method mixed lPush($key, $value)
 * @method mixed lPushx($key, $value)
 * @method mixed lSet($key, $index, $value)
 * @method mixed lastSave()
 * @method mixed lindex($key, $index)
 * @method mixed lrange($key, $start, $end)
 * @method mixed lrem($key, $value, $count)
 * @method mixed ltrim($key, $start, $stop)
 * @method mixed mget(array $keys)
 * @method mixed migrate($host, $port, $key, $db, $timeout, $copy = null, $replace = null)
 * @method mixed move($key, $dbindex)
 * @method mixed mset(array $pairs)
 * @method mixed msetnx(array $pairs)
 * @method mixed multi($mode = null)
 * @method mixed object($field, $key)
 * @method mixed pconnect($host, $port = null, $timeout = null)
 * @method mixed persist($key)
 * @method mixed pexpire($key, $timestamp)
 * @method mixed pexpireAt($key, $timestamp)
 * @method mixed pfadd($key, array $elements)
 * @method mixed pfcount($key)
 * @method mixed pfmerge($dstkey, array $keys)
 * @method mixed ping()
 * @method mixed pipeline()
 * @method mixed psetex($key, $expire, $value)
 * @method mixed psubscribe(array $patterns, $callback)
 * @method mixed pttl($key)
 * @method mixed publish($channel, $message)
 * @method mixed pubsub($cmd, ...$args)
 * @method mixed punsubscribe($pattern, ...$other_patterns)
 * @method mixed rPop($key)
 * @method mixed rPush($key, $value)
 * @method mixed rPushx($key, $value)
 * @method mixed randomKey()
 * @method mixed rawcommand($cmd, ...$args)
 * @method mixed rename($key, $newkey)
 * @method mixed renameNx($key, $newkey)
 * @method mixed restore($ttl, $key, $value)
 * @method mixed role()
 * @method mixed rpoplpush($src, $dst)
 * @method mixed sAdd($key, $value)
 * @method mixed sAddArray($key, array $options)
 * @method mixed sDiff($key, ...$other_keys)
 * @method mixed sDiffStore($dst, $key, ...$other_keys)
 * @method mixed sInter($key, ...$other_keys)
 * @method mixed sInterStore($dst, $key, ...$other_keys)
 * @method mixed sMembers($key)
 * @method mixed sMove($src, $dst, $value)
 * @method mixed sPop($key)
 * @method mixed sRandMember($key, $count = null)
 * @method mixed sUnion($key, ...$other_keys)
 * @method mixed sUnionStore($dst, $key, ...$other_keys)
 * @method mixed save()
 * @method mixed scard($key)
 * @method mixed script($cmd, ...$args)
 * @method mixed select($dbindex)
 * @method mixed set($key, $value, $opts = null)
 * @method mixed setBit($key, $offset, $value)
 * @method mixed setOption($option, $value)
 * @method mixed setRange($key, $offset, $value)
 * @method mixed setex($key, $expire, $value)
 * @method mixed setnx($key, $value)
 * @method mixed sismember($key, $value)
 * @method mixed slaveof($host = null, $port = null)
 * @method mixed slowlog($arg, $option = null)
 * @method mixed sort($key, array $options = null)
 * @method mixed sortAsc($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortAscAlpha($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortDesc($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortDescAlpha($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed srem($key, $member, ...$other_members)
 * @method mixed strlen($key)
 * @method mixed subscribe(array $channels, $callback)
 * @method mixed swapdb($srcdb, $dstdb)
 * @method mixed time()
 * @method mixed ttl($key)
 * @method mixed type($key)
 * @method mixed unlink($key, ...$other_keys)
 * @method mixed unsubscribe($channel, ...$other_channels)
 * @method mixed unwatch()
 * @method mixed wait($numslaves, $timeout)
 * @method mixed watch($key, ...$other_keys)
 * @method mixed xack($str_key, $str_group, array $arr_ids)
 * @method mixed xadd($str_key, $str_id, array $arr_fields, $i_maxlen = null, $boo_approximate = null)
 * @method mixed xclaim($str_key, $str_group, $str_consumer, $i_min_idle, array $arr_ids, array $arr_opts = null)
 * @method mixed xdel($str_key, array $arr_ids)
 * @method mixed xgroup($str_operation, $str_key = null, $str_arg1 = null, $str_arg2 = null, $str_arg3 = null)
 * @method mixed xinfo($str_cmd, $str_key = null, $str_group = null)
 * @method mixed xlen($key)
 * @method mixed xpending($str_key, $str_group, $str_start = null, $str_end = null, $i_count = null, $str_consumer = null)
 * @method mixed xrange($str_key, $str_start, $str_end, $i_count = null)
 * @method mixed xread(array $arr_streams, $i_count = null, $i_block = null)
 * @method mixed xreadgroup($str_group, $str_consumer, array $arr_streams, $i_count = null, $i_block = null)
 * @method mixed xrevrange($str_key, $str_start, $str_end, $i_count = null)
 * @method mixed xtrim($str_key, $i_maxlen, $boo_approximate = null)
 * @method mixed zAdd($key, $score, $value)
 * @method mixed zCard($key)
 * @method mixed zCount($key, $min, $max)
 * @method mixed zIncrBy($key, $value, $member)
 * @method mixed zLexCount($key, $min, $max)
 * @method mixed zPopMax($key)
 * @method mixed zPopMin($key)
 * @method mixed zRange($key, $start, $end, $scores = null)
 * @method mixed zRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method mixed zRangeByScore($key, $start, $end, array $options = null)
 * @method mixed zRank($key, $member)
 * @method mixed zRem($key, $member, ...$other_members)
 * @method mixed zRemRangeByLex($key, $min, $max)
 * @method mixed zRemRangeByRank($key, $start, $end)
 * @method mixed zRemRangeByScore($key, $min, $max)
 * @method mixed zRevRange($key, $start, $end, $scores = null)
 * @method mixed zRevRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method mixed zRevRangeByScore($key, $start, $end, array $options = null)
 * @method mixed zRevRank($key, $member)
 * @method mixed zScore($key, $member)
 * @method mixed zinterstore($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed zunionstore($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed delete($key, ...$other_keys)
 * @method mixed evaluate($script, $args = null, $num_keys = null)
 * @method mixed evaluateSha($script_sha, $args = null, $num_keys = null)
 * @method mixed getKeys($pattern)
 * @method mixed getMultiple(array $keys)
 * @method mixed lGet($key, $index)
 * @method mixed lGetRange($key, $start, $end)
 * @method mixed lRemove($key, $value, $count)
 * @method mixed lSize($key)
 * @method mixed listTrim($key, $start, $stop)
 * @method mixed open($host, $port = null, $timeout = null, $retry_interval = null)
 * @method mixed popen($host, $port = null, $timeout = null)
 * @method mixed renameKey($key, $newkey)
 * @method mixed sContains($key, $value)
 * @method mixed sGetMembers($key)
 * @method mixed sRemove($key, $member, ...$other_members)
 * @method mixed sSize($key)
 * @method mixed sendEcho($msg)
 * @method mixed setTimeout($key, $timeout)
 * @method mixed substr($key, $start, $end)
 * @method mixed zDelete($key, $member, ...$other_members)
 * @method mixed zDeleteRangeByRank($key, $min, $max)
 * @method mixed zDeleteRangeByScore($key, $min, $max)
 * @method mixed zInter($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed zRemove($key, $member, ...$other_members)
 * @method mixed zRemoveRangeByScore($key, $min, $max)
 * @method mixed zReverseRange($key, $start, $end, $scores = null)
 * @method mixed zSize($key)
 * @method mixed zUnion($key, array $keys, ?array $weights = null, $aggregate = null)
 */
class RedisHandler
{
    /**
     * redis 对象
     *
     * @var \Redis|\RedisCluster
     */
    private $redis;

    /**
     * 连接主机名.
     */
    private string $host = '';

    /**
     * 连接端口号.
     */
    private int $port = 0;

    /**
     * 连接超时时间.
     */
    private float $timeout = 0;

    /**
     * 登录凭证
     *
     * @var mixed
     */
    private $auth = null;

    /**
     * @param \Redis|\RedisCluster $redis
     */
    public function __construct($redis)
    {
        $this->redis = $redis;
        if (!$this->isCluster() && $redis->isConnected())
        {
            $this->host = $redis->getHost();
            $this->port = $redis->getPort();
            $this->timeout = $redis->getTimeout();
            $this->auth = $redis->getAuth();
        }
    }

    /**
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $redis = $this->redis;
        $result = $redis->$name(...$arguments);
        if (!$this->isCluster())
        {
            switch ($name)
            {
                case 'connect':
                case 'open':
                    if ($redis->isConnected())
                    {
                        $this->host = $redis->getHost();
                        $this->port = $redis->getPort();
                        $this->timeout = $redis->getTimeout();
                    }
                    // no break
                case 'auth':
                    $this->auth = $redis->getAuth();
                    break;
            }
        }

        return $result;
    }

    /**
     * 获取 Redis 对象实例.
     *
     * @return \Redis|\RedisCluster
     */
    public function getInstance()
    {
        return $this->redis;
    }

    /**
     * 重新连接.
     */
    public function reconnect(): bool
    {
        $redis = $this->redis;
        $redis->close();
        if (!$this->isCluster())
        {
            if ($redis->connect($this->host, $this->port, $this->timeout))
            {
                $auth = $this->auth;
                if (null !== $auth && !$redis->auth($auth))
                {
                    throw new \RedisException($redis->getLastError());
                }
            }
            else
            {
                throw new \RedisException($redis->getLastError());
            }
        }

        return true;
    }

    /**
     * eval扩展方法，结合了 eval、evalSha.
     *
     * 优先使用 evalSha 尝试，失败则使用 eval 方法
     *
     * @return mixed
     */
    public function evalEx(string $script, ?array $args = null, ?int $numKeys = null)
    {
        $sha1 = sha1($script);
        $this->clearLastError();
        // @phpstan-ignore-next-line
        $result = $this->evalSha($sha1, $args, $numKeys);
        $error = $this->getLastError();
        if ($error)
        {
            if ('NOSCRIPT No matching script. Please use EVAL.' === $error)
            {
                $this->clearLastError();
                $result = $this->eval($script, $args, $numKeys);
                $error = $this->getLastError();
                if ($error)
                {
                    throw new RedisException($error);
                }
            }
            else
            {
                throw new RedisException($error);
            }
        }

        return $result;
    }

    /**
     * scan.
     *
     * @param mixed $strNode
     *
     * @return mixed
     */
    public function scan(?int &$iterator, ?string $pattern = null, int $count = 0, $strNode = null)
    {
        if (null === $strNode)
        {
            return $this->redis->scan($iterator, $pattern, $count);
        }
        else
        {
            // @phpstan-ignore-next-line
            return $this->redis->scan($iterator, $strNode, $pattern, $count);
        }
    }

    /**
     * scan 方法的扩展简易遍历方法.
     *
     * @return mixed
     */
    public function scanEach(?string $pattern = null, int $count = 0)
    {
        $redis = $this->redis;
        if ($this->isCluster())
        {
            // @phpstan-ignore-next-line
            foreach ($redis->_masters() as $master)
            {
                $it = null;
                // @phpstan-ignore-next-line
                while (false !== ($keys = $redis->scan($it, $master, $pattern, $count)))
                {
                    if ($keys)
                    {
                        foreach ($keys as $key)
                        {
                            yield $key;
                        }
                    }
                }
            }
        }
        else
        {
            $it = null;
            while (false !== ($keys = $redis->scan($it, $pattern, $count)))
            {
                if ($keys)
                {
                    foreach ($keys as $key)
                    {
                        yield $key;
                    }
                }
            }
        }
    }

    /**
     * hscan.
     *
     * @return mixed
     */
    public function hscan(string $key, ?int &$iterator, ?string $pattern = null, int $count = 0)
    {
        return $this->redis->hscan($key, $iterator, $pattern, $count);
    }

    /**
     * hscan 方法的扩展简易遍历方法.
     *
     * @return mixed
     */
    public function hscanEach(string $key, ?string $pattern = null, int $count = 0)
    {
        $it = null;
        while (false !== ($result = $this->hscan($key, $it, $pattern, $count)))
        {
            if ($result)
            {
                foreach ($result as $key => $value)
                {
                    yield $key => $value;
                }
            }
        }
    }

    /**
     * sscan.
     *
     * @return mixed
     */
    public function sscan(string $key, ?int &$iterator, ?string $pattern = null, int $count = 0)
    {
        return $this->redis->sscan($key, $iterator, $pattern, $count);
    }

    /**
     * sscan 方法的扩展简易遍历方法.
     *
     * @return mixed
     */
    public function sscanEach(string $key, ?string $pattern = null, int $count = 0)
    {
        $it = null;
        while (false !== ($result = $this->sscan($key, $it, $pattern, $count)))
        {
            if ($result)
            {
                foreach ($result as $value)
                {
                    yield $value;
                }
            }
        }
    }

    /**
     * zscan.
     *
     * @return mixed
     */
    public function zscan(string $key, ?int &$iterator, ?string $pattern = null, int $count = 0)
    {
        return $this->redis->zscan($key, $iterator, $pattern, $count);
    }

    /**
     * zscan 方法的扩展简易遍历方法.
     *
     * @return mixed
     */
    public function zscanEach(string $key, ?string $pattern = null, int $count = 0)
    {
        $it = null;
        while (false !== ($result = $this->zscan($key, $it, $pattern, $count)))
        {
            if ($result)
            {
                foreach ($result as $value)
                {
                    yield $value;
                }
            }
        }
    }

    /**
     * 是否为集群.
     */
    public function isCluster(): bool
    {
        return $this->redis instanceof \RedisCluster;
    }
}
