<?php
namespace Imi\Redis;

/**
 * imi 框架中封装的 Redis 类
 * 
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
 * @method mixed clearLastError()
 * @method mixed client($cmd, ...$args)
 * @method mixed close()
 * @method mixed command(...$args)
 * @method mixed config($cmd, $key, $value = null)
 * @method mixed connect($host, $port, $timeout = null, $retry_interval = null)
 * @method mixed dbSize()
 * @method mixed debug($key)
 * @method mixed decr($key)
 * @method mixed decrBy($key, $value)
 * @method mixed delete($key, ...$other_keys)
 * @method mixed discard()
 * @method mixed dump($key)
 * @method mixed echo($msg)
 * @method mixed eval($script, $args = null, $num_keys = null)
 * @method mixed evalsha($script_sha, $args = null, $num_keys = null)
 * @method mixed exec()
 * @method mixed exists($key, ...$other_keys)
 * @method mixed expireAt($key, $timestamp)
 * @method mixed flushAll()
 * @method mixed flushDB()
 * @method mixed geoadd($key, $lng, $lat, $member, ...$other_triples)
 * @method mixed geodist($key, $src, $dst, $unit = null)
 * @method mixed geohash($key, $member, ...$other_members)
 * @method mixed geopos($key, $member, ...$other_members)
 * @method mixed georadius($key, $lng, $lan, $radius, $unit, array $opts = null)
 * @method mixed georadiusbymember($key, $member, $radius, $unit, array $opts = null)
 * @method mixed get($key)
 * @method mixed getAuth()
 * @method mixed getBit($key, $offset)
 * @method mixed getDBNum()
 * @method mixed getHost()
 * @method mixed getKeys($pattern)
 * @method mixed getLastError()
 * @method mixed getMode()
 * @method mixed getMultiple(array $keys)
 * @method mixed getOption($option)
 * @method mixed getPersistentID()
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
 * @method mixed lGet($key, $index)
 * @method mixed lGetRange($key, $start, $end)
 * @method mixed lInsert($key, $position, $pivot, $value)
 * @method mixed lPop($key)
 * @method mixed lPush($key, $value)
 * @method mixed lPushx($key, $value)
 * @method mixed lRemove($key, $value, $count)
 * @method mixed lSet($key, $index, $value)
 * @method mixed lSize($key)
 * @method mixed lastSave()
 * @method mixed listTrim($key, $start, $stop)
 * @method mixed migrate($host, $port, $key, $db, $timeout, $copy = null, $replace = null)
 * @method mixed move($key, $dbindex)
 * @method mixed mset(array $pairs)
 * @method mixed msetnx(array $pairs)
 * @method mixed multi()
 * @method mixed object($field, $key)
 * @method mixed pconnect($host, $port, $timeout = null)
 * @method mixed persist($key)
 * @method mixed pexpire($key, $timestamp)
 * @method mixed pexpireAt($key, $timestamp)
 * @method mixed pfadd($key, array $elements)
 * @method mixed pfcount($key)
 * @method mixed pfmerge($dstkey, array $keys)
 * @method mixed ping()
 * @method mixed pipeline()
 * @method mixed psetex($key, $expire, $value)
 * @method mixed psubscribe(array $patterns)
 * @method mixed pttl($key)
 * @method mixed publish($channel, $message)
 * @method mixed pubsub($cmd, ...$args)
 * @method mixed punsubscribe($pattern, ...$other_patterns)
 * @method mixed rPop($key)
 * @method mixed rPush($key, $value)
 * @method mixed rPushx($key, $value)
 * @method mixed randomKey()
 * @method mixed rawcommand($cmd, ...$args)
 * @method mixed renameKey($key, $newkey)
 * @method mixed renameNx($key, $newkey)
 * @method mixed restore($ttl, $key, $value)
 * @method mixed role()
 * @method mixed rpoplpush($src, $dst)
 * @method mixed sAdd($key, $value)
 * @method mixed sAddArray($key, array $options)
 * @method mixed sContains($key, $value)
 * @method mixed sDiff($key, ...$other_keys)
 * @method mixed sDiffStore($dst, $key, ...$other_keys)
 * @method mixed sInter($key, ...$other_keys)
 * @method mixed sInterStore($dst, $key, ...$other_keys)
 * @method mixed sMembers($key)
 * @method mixed sMove($src, $dst, $value)
 * @method mixed sPop($key)
 * @method mixed sRandMember($key, $count = null)
 * @method mixed sRemove($key, $value)
 * @method mixed sSize($key)
 * @method mixed sUnion($key, ...$other_keys)
 * @method mixed sUnionStore($dst, $key, ...$other_keys)
 * @method mixed save()
 * @method mixed script($cmd, ...$args)
 * @method mixed select($dbindex)
 * @method mixed set($key, $value, $timeout = null, $opt = null)
 * @method mixed setBit($key, $offset, $value)
 * @method mixed setOption($option, $value)
 * @method mixed setRange($key, $offset, $value)
 * @method mixed setTimeout($key, $timeout)
 * @method mixed setex($key, $expire, $value)
 * @method mixed setnx($key, $value)
 * @method mixed slaveof($host = null, $port = null)
 * @method mixed slowlog($arg, $option = null)
 * @method mixed sort($key, array $options = null)
 * @method mixed sortAsc($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortAscAlpha($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortDesc($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed sortDescAlpha($key, $pattern = null, $get = null, $start = null, $end = null, $getList = null)
 * @method mixed strlen($key)
 * @method mixed subscribe(array $channels)
 * @method mixed swapdb($srcdb, $dstdb)
 * @method mixed time()
 * @method mixed ttl($key)
 * @method mixed type($key)
 * @method mixed unlink($key, ...$other_keys)
 * @method mixed unsubscribe($channel, ...$other_channels)
 * @method mixed unwatch()
 * @method mixed wait($numslaves, $timeout)
 * @method mixed watch($key, ...$other_keys)
 * @method mixed zAdd($key, $score, $value)
 * @method mixed zCard($key)
 * @method mixed zCount($key, $min, $max)
 * @method mixed zDelete($key, $member, ...$other_members)
 * @method mixed zDeleteRangeByRank($key, $start, $end)
 * @method mixed zDeleteRangeByScore($key, $min, $max)
 * @method mixed zIncrBy($key, $value, $member)
 * @method mixed zInter($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed zLexCount($key, $min, $max)
 * @method mixed zRange($key, $start, $end, $scores = null)
 * @method mixed zRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method mixed zRangeByScore($key, $start, $end, array $options = null)
 * @method mixed zRank($key, $member)
 * @method mixed zRemRangeByLex($key, $min, $max)
 * @method mixed zRevRange($key, $start, $end, $scores = null)
 * @method mixed zRevRangeByLex($key, $min, $max, $offset = null, $limit = null)
 * @method mixed zRevRangeByScore($key, $start, $end, array $options = null)
 * @method mixed zRevRank($key, $member)
 * @method mixed zScore($key, $member)
 * @method mixed zUnion($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed del($key, ...$other_keys)
 * @method mixed evaluate($script, $args = null, $num_keys = null)
 * @method mixed evaluateSha($script_sha, $args = null, $num_keys = null)
 * @method mixed expire($key, $timeout)
 * @method mixed keys($pattern)
 * @method mixed lLen($key)
 * @method mixed lindex($key, $index)
 * @method mixed lrange($key, $start, $end)
 * @method mixed lrem($key, $value, $count)
 * @method mixed ltrim($key, $start, $stop)
 * @method mixed mget(array $keys)
 * @method mixed open($host, $port, $timeout = null, $retry_interval = null)
 * @method mixed popen($host, $port, $timeout = null)
 * @method mixed rename($key, $newkey)
 * @method mixed sGetMembers($key)
 * @method mixed scard($key)
 * @method mixed sendEcho($msg)
 * @method mixed sismember($key, $value)
 * @method mixed srem($key, $value)
 * @method mixed substr($key, $start, $end)
 * @method mixed zRem($key, $member, ...$other_members)
 * @method mixed zRemRangeByRank($key, $min, $max)
 * @method mixed zRemRangeByScore($key, $min, $max)
 * @method mixed zRemove($key, $member, ...$other_members)
 * @method mixed zRemoveRangeByScore($key, $min, $max)
 * @method mixed zReverseRange($key, $start, $end, $scores = null)
 * @method mixed zSize($key)
 * @method mixed zinterstore($key, array $keys, ?array $weights = null, $aggregate = null)
 * @method mixed zunionstore($key, array $keys, ?array $weights = null, $aggregate = null)
 */
class RedisHandler
{
    /**
     * redis 对象
     *
     * @var \Redis|\Swoole\Coroutine\Redis
     */
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function __call($name, $arguments)
    {
        return $this->redis->$name(...$arguments);
    }

    /**
     * 获取 Redis 对象实例
     *
     * @return \Redis|\Swoole\Coroutine\Redis
     */
    public function getInstance()
    {
        return $this->redis;
    }

    /**
     * 获取最后错误信息
     *
     * @return string
     */
    public function getLastError()
    {
        if($this->redis instanceof \Swoole\Coroutine\Redis)
        {
            return $this->redis->errMsg;
        }
        else
        {
            return $this->redis->getLastError();
        }
    }

    public function hscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        return $this->redis->hscan($str_key, $i_iterator, $str_pattern, $i_count);
    }

    public function scan(&$i_iterator, $str_pattern = null, $i_count = null)
    {
        return $this->redis->scan($i_iterator, $str_pattern, $i_count);
    }

    public function zscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        return $this->redis->zscan($str_key, $i_iterator, $str_pattern, $i_count);
    }

    public function sscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        return $this->redis->sscan($str_key, $i_iterator, $str_pattern, $i_count);
    }

    /**
     * eval扩展方法，结合了 eval、evalSha
     * 
     * 优先使用 evalSha 尝试，失败则使用 eval 方法
     *
     * @param string $script
     * @param array $args
     * @param int $num_keys
     * @return mixed
     */
    public function evalEx($script, $args = null, $num_keys = null)
    {
        $sha1 = sha1($script);
        $this->clearLastError();
        $result = $this->evalSha($sha1, $args, $num_keys);
        if('NOSCRIPT No matching script. Please use EVAL.' === $this->getLastError())
        {
            $result = $this->eval($script, $args, $num_keys);
        }
        return $result;
    }

}
