<?php
namespace Imi;

use Imi\Util\Coroutine;
use Imi\Server\Base;
use Imi\Bean\Container;
use Imi\Event\Event;
use Imi\Exception\RequestContextException;

abstract class RequestContext
{
    private static $context = [];

    /**
     * 为当前请求创建上下文，返回当前协程ID
     * 
     * @param array $data
     * @return int
     */
    public static function create(array $data = [])
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            static::$context[$coID] = $data;
            if($coID > -1)
            {
                defer(function(){
                    try {
                        RequestContext::destroy();
                    } catch(RequestContextException $rce) {

                    }
                });
            }
            Event::trigger('IMI.REQUEST_CONTENT.CREATE');
            return $coID;
        }
        else
        {
            throw new RequestContextException(sprintf('Create context failed, cannot create a duplicate context in cid %s', $coID));
        }
    }

    /**
     * 销毁当前请求的上下文
     * @return void
     */
    public static function destroy()
    {
        $coID = Coroutine::getuid();
        if(isset(static::$context[$coID]))
        {
            Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
            unset(static::$context[$coID]);
        }
        else
        {
            throw new RequestContextException('Destroy context failed, current context is not found');
        }
    }

    /**
     * 判断当前请求上下文是否存在
     * @return boolean
     */
    public static function exists()
    {
        return isset(static::$context[Coroutine::getuid()]);
    }

    /**
     * 获取上下文数据
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            throw new RequestContextException('get context data failed, current context is not found');
        }
        if(isset(static::$context[$coID][$name]))
        {
            return static::$context[$coID][$name];
        }
        else
        {
            return $default;
        }
    }

    /**
     * 设置上下文数据
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set($name, $value)
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            throw new RequestContextException('set context data failed, current context is not found');
        }
        static::$context[$coID][$name] = $value;
    }

    /**
     * 批量设置上下文数据
     *
     * @param array $data
     * @return void
     */
    public static function muiltiSet(array $data)
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            throw new RequestContextException('set context data failed, current context is not found');
        }
        foreach($data as $k => $v)
        {
            static::$context[$coID][$k] = $v;
        }
    }

    /**
     * 使用回调来使用当前请求上下文数据
     *
     * @param callable $callback
     * @return mixed
     */
    public static function use(callable $callback)
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            throw new RequestContextException('set context data failed, current context is not found');
        }
        return $callback(static::$context[$coID]);
    }

    /**
     * 获取当前上下文
     * @return array
     */
    public static function getContext()
    {
        $coID = Coroutine::getuid();
        if(!isset(static::$context[$coID]))
        {
            throw new RequestContextException('get context failed, current context is not found');
        }
        return static::$context[$coID];
    }

    /**
     * 获取当前的服务器对象
     * @return \Imi\Server\Base|null
     */
    public static function getServer()
    {
        return static::get('server');
    }

    /**
     * 在当前服务器上下文中获取Bean对象
     * @param string $name
     * @return mixed
     */
    public static function getServerBean($name, ...$params)
    {
        return static::getServer()->getBean($name, ...$params);
    }

    /**
     * 在当前请求上下文中获取Bean对象
     * @param string $name
     * @return mixed
     */
    public static function getBean($name, ...$params)
    {
        $container = static::get('container');
        if(null === $container)
        {
            $container = new Container;
            static::set('container', $container);
        }
        return $container->get($name, ...$params);
    }

}