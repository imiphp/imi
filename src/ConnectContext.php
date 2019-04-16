<?php
namespace Imi;

use Imi\RequestContext;

abstract class ConnectContext
{
    private static $context = [];

    /**
     * 为当前请求创建上下文
     * @return void
     */
    public static function create()
    {
        $key = static::getContextKey();
        if(!isset(static::$context[$key]))
        {
            static::$context[$key] = RequestContext::getServerBean('ConnectContextStore')->read($key);
        }
    }

    /**
     * 销毁当前请求的上下文
     * 
     * @param int|null $fd
     * @return void
     */
    public static function destroy($fd = null)
    {
        $key = static::getContextKey($fd);
        if(isset(static::$context[$key]))
        {
            unset(static::$context[$key]);
        }
        RequestContext::getServerBean('ConnectContextStore')->destroy($key);
    }

    /**
     * 判断当前请求上下文是否存在
     * @deprecated 1.0
     * @return boolean
     */
    public static function exsits()
    {
        return static::exists();
    }

    /**
     * 判断当前请求上下文是否存在
     * @return boolean
     */
    public static function exists()
    {
        if(RequestContext::exists())
        {
            $key = static::getContextKey();
            return isset(static::$context[$key]) || RequestContext::getServerBean('ConnectContextStore')->exists($key);
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取上下文数据
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $key = static::getContextKey();
        if(!isset(static::$context[$key]))
        {
            static::$context[$key] = RequestContext::getServerBean('ConnectContextStore')->read($key);
        }
        return static::$context[$key][$name] ?? $default;
    }

    /**
     * 设置上下文数据
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set($name, $value)
    {
        $key = static::getContextKey();
        $store = RequestContext::getServerBean('ConnectContextStore');
        if(!isset(static::$context[$key]))
        {
            static::$context[$key] = $store->read($key);
        }
        static::$context[$key][$name] = $value;
        $store->save($key, static::$context[$key]);
    }

    /**
     * 获取当前上下文
     * @return array
     */
    public static function getContext()
    {
        $key = static::getContextKey();
        return static::$context[$key] ?? null;
    }

    /**
     * 获取上下文的key
     *
     * @param int|null $fd
     * @return string
     */
    private static function getContextKey($fd = null)
    {
        return RequestContext::getServer()->getName() . '-' . ($fd ?? RequestContext::get('fd'));
    }
}