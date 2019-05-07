<?php
namespace Imi\Redis;

trait TFixSwoole
{
    /**
     * sscan/hscan/zscan
     *
     * @param string $method
     * @param string $str_key
     * @param int|null $i_iterator
     * @param string $str_pattern
     * @param int $i_count
     * @return array|boolean
     */
    protected function xxxScan($method, $str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        $argStrs = ['ARGV[1]', 'ARGV[2]'];
        $args = [$str_key, $i_iterator];

        $argvCounter = 2;

        if(null !== $str_pattern)
        {
            $argStrs[] = "'MATCH'";
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = $str_pattern;
        }

        if(null !== $i_count)
        {
            $argStrs[] = "'COUNT'";
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = $i_count;
        }

        $argStr = implode(',', $argStrs);

        $result = $this->eval(<<<STR
return redis.call('{$method}', {$argStr}) 
STR
        , $args);

        if(!$result)
        {
            return false;
        }

        $i_iterator = (int)$result[0];
        return 0 === $i_iterator && [] === $result[1] ? false : $result[1];
    }

    /**
     * scan
     *
     * @param int|null $i_iterator
     * @param string $str_pattern
     * @param int $i_count
     * @return array|boolean
     */
    public function scan(&$i_iterator, $str_pattern = null, $i_count = null)
    {
        $argStrs = ['ARGV[1]'];
        $args = [$i_iterator];

        $argvCounter = 1;

        if(null !== $str_pattern)
        {
            $argStrs[] = "'MATCH'";
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = $str_pattern;
        }

        if(null !== $i_count)
        {
            $argStrs[] = "'COUNT'";
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = $i_count;
        }

        $argStr = implode(',', $argStrs);

        $result = $this->eval(<<<STR
return redis.call('scan', {$argStr}) 
STR
        , $args);

        if(!$result)
        {
            return false;
        }

        $i_iterator = (int)$result[0];
        return 0 === $i_iterator && [] === $result[1] ? false : $result[1];
    }

    /**
     * sscan
     *
     * @param string $str_key
     * @param int|null $i_iterator
     * @param string $str_pattern
     * @param int $i_count
     * @return array|boolean
     */
    public function sscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        return $this->xxxScan('sscan', $str_key, $i_iterator, $str_pattern, $i_count);
    }

    /**
     * object
     *
     * @param string $field
     * @param string $key
     * @return string
     */
    public function object($field, $key)
    {
        $argStrs = ['ARGV[1]', 'ARGV[2]'];
        $args = [$field, $key];

        $argStr = implode(',', $argStrs);

        $result = $this->eval(<<<STR
return redis.call('object', {$argStr}) 
STR
        , $args);

        return $result;
    }

    /**
     * sort
     *
     * @param string $key
     * @param array $options
     * @return void
     */
    public function sort($key, array $options = null)
    {
        $argStrs = ['ARGV[1]'];
        $args = [$key];

        $argvCounter = 1;

        foreach($options as $k => $v)
        {
            switch(strtolower($k))
            {
                case 'by':
                    $argStrs[] = "'BY'";
                    $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                    $args[] = $v;
                    break;
                case 'limit':
                    $argStrs[] = "'LIMIT'";
                    $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                    $args[] = $v[0];
                    $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                    $args[] = $v[1];
                    break;
                case 'get':
                    foreach(is_array($v) ? $v : [$v] as $pattern)
                    {
                        $argStrs[] = "'GET'";
                        $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                        $args[] = $pattern;
                    }
                    break;
                case 'sort':
                    $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                    $args[] = $v;
                    break;
                case 'alpha':
                    if($v)
                    {
                        $argStrs[] = "'ALPHA'";
                    }
                    break;
                case 'store':
                    $argStrs[] = "'STORE'";
                    $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
                    $args[] = $v;
                    break;
            }
        }

        $argStr = implode(',', $argStrs);

        $result = $this->eval(<<<STR
return redis.call('sort', {$argStr}) 
STR
        , $args);

        return $result;
    }

    /**
     * migrate
     *
     * @param string $host
     * @param int $port
     * @param string $key
     * @param int $db
     * @param int $timeout
     * @param boolean|null $copy
     * @param boolean|null $replace
     * @return void
     */
    public function migrate($host, $port, $key, $db, $timeout, $copy = null, $replace = null)
    {
        $argStrs = [];
        $args = [$host, $port, $key, $db, $timeout];

        $argvCounter = 0;

        foreach($args as $item)
        {
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
        }

        if($copy)
        {
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = 'COPY';
        }

        if($replace)
        {
            $argStrs[] = 'ARGV[' . (++$argvCounter) . ']';
            $args[] = 'REPLACE';
        }

        $argStr = implode(',', $argStrs);

        $result = $this->eval(<<<STR
return redis.call('migrate', {$argStr}) 
STR
        , $args);

        return $result;
    }

    /**
     * hscan
     *
     * @param string $str_key
     * @param int|null $i_iterator
     * @param string $str_pattern
     * @param int $i_count
     * @return array|boolean
     */
    public function hscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        $list = $this->xxxScan('hscan', $str_key, $i_iterator, $str_pattern, $i_count);
        if(false === $list)
        {
            return false;
        }
        $result = [];
        $length = count($list);
        for($i = 0; $i < $length; $i += 2)
        {
            $result[$list[$i]] = $list[$i + 1];
        }
        return $result;
    }

    /**
     * zscan
     *
     * @param string $str_key
     * @param int|null $i_iterator
     * @param string $str_pattern
     * @param int $i_count
     * @return array|boolean
     */
    public function zscan($str_key, &$i_iterator, $str_pattern = null, $i_count = null)
    {
        $list = $this->xxxScan('zscan', $str_key, $i_iterator, $str_pattern, $i_count);
        if(false === $list)
        {
            return false;
        }
        $result = [];
        $length = count($list);
        for($i = 0; $i < $length; $i += 2)
        {
            $result[$list[$i]] = $list[$i + 1];
        }
        return $result;
    }
}