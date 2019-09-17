<?php
namespace Imi;

use Imi\RequestContext;

abstract class ConnectContext
{
    /**
     * 为当前连接创建上下文
     * @return void
     */
    public static function create(array $data = [])
    {
        $data = static::get();
        if(!$data && $fd = RequestContext::get('fd'))
        {
            static::use(function($contextData) use($data, $fd){
                $contextData = $data;
                $contextData['fd'] = $fd;
                return $contextData;
            }, $fd);
        }
    }

    /**
     * 销毁当前连接的上下文
     * 
     * @param int|null $fd
     * @return void
     */
    public static function destroy($fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        RequestContext::getServerBean('ConnectContextStore')->destroy($fd);
    }

    /**
     * 判断当前连接上下文是否存在
     * @param int|null $fd
     * @return boolean
     */
    public static function exists($fd = null)
    {
        try {
            if(!$fd)
            {
                $fd = RequestContext::get('fd');
            }
            return isset(static::$context[$fd]) || RequestContext::getServerBean('ConnectContextStore')->exists($fd);
        } catch(\Imi\Exception\RequestContextException $e) {
            return false;
        }
    }

    /**
     * 获取上下文数据
     * @param string|null $name
     * @param mixed $default
     * @param int|null $fd
     * @return mixed
     */
    public static function get($name = null, $default = null, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $data = RequestContext::getServerBean('ConnectContextStore')->read($fd);
        if(null === $name)
        {
            return $data;
        }
        else
        {
            return $data[$name] ?? $default;
        }
    }

    /**
     * 设置上下文数据
     * @param string $name
     * @param mixed $value
     * @param int|null $fd
     * @return void
     */
    public static function set($name, $value, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $result = $store->lock(function() use($store, $name, $value, $fd){
            $data = $store->read($fd);
            $data[$name] = $value;
            $store->save($fd, $data);
        });
        if(!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 批量设置上下文数据
     *
     * @param array $data
     * @param int|null $fd
     * @return void
     */
    public static function muiltiSet(array $data, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $result = $store->lock(function() use($store, $data, $fd){
            $data = $store->read($fd);
            foreach($data as $name => $value)
            {
                $data[$name] = $value;
            }
            $store->save($fd, $data);
        });
        if(!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文
     *
     * @param callable $callable
     * @param int|null $fd
     * @return void
     */
    public static function use($callable, $fd = null)
    {
        if(!$fd)
        {
            $fd = RequestContext::get('fd');
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $store->lock(function() use($callable, $store, $fd){
            $data = $store->read($fd);
            $result = $callable($data);
            if($result)
            {
                $store->save($fd, $result);
            }
        });
    }

    /**
     * 获取当前上下文
     * @param int|null $fd
     * @return array
     */
    public static function getContext($fd = null)
    {
        return static::get(null, null, $fd);
    }

}