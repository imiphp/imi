<?php
namespace Imi;

use Imi\Event\Event;
use Imi\Bean\Container;
use Imi\Util\Coroutine;

abstract class RequestContext
{
    /**
     * 上下文集合
     *
     * @var array
     */
    private static $contextMap = [];

    /**
     * 为当前请求创建上下文，返回当前协程ID
     * 
     * @param array $data
     * @return int
     * @deprecated 1.0.17
     */
    public static function create(array $data = [])
    {
        return Coroutine::getuid();
    }

    /**
     * 销毁当前请求的上下文
     * @return void
     * @deprecated 1.0.17
     */
    public static function destroy()
    {
    }

    /**
     * 判断当前请求上下文是否存在
     * @return boolean
     * @deprecated 1.0.17
     */
    public static function exists()
    {
        return true;
    }
    
    /**
     * 销毁当前请求的上下文
     * @return void
     */
    public static function __destroy()
    {
        Event::trigger('IMI.REQUEST_CONTENT.DESTROY');
        $context = Coroutine::getContext();
        if(!$context)
        {
            $coId = Coroutine::getuid();
            $contextMap = &static::$contextMap;
            if(isset($contextMap[$coId]))
            {
                unset($contextMap[$coId]);
            }
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
        $context = Coroutine::getContext();
        if($context)
        {
            return $context[$name] ?? $default;
        }
        return static::$contextMap[Coroutine::getuid()][$name] ?? $default;
    }

    /**
     * 设置上下文数据
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set($name, $value)
    {
        $context = Coroutine::getContext();
        if($context)
        {
            if(!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([static::class, '__destroy']);
            }
        }
        else
        {
            $coId = Coroutine::getuid();
            $contextMap = &static::$contextMap;
            if(isset($contextMap[$coId]))
            {
                $context = $contextMap[$coId];
            }
            else
            {
                $context = $contextMap[$coId] = new \Swoole\Coroutine\Context;
            }
        }
        $context[$name] = $value;
    }

    /**
     * 批量设置上下文数据
     *
     * @param array $data
     * @return void
     */
    public static function muiltiSet(array $data)
    {
        $context = Coroutine::getContext();
        if($context)
        {
            if(!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([static::class, '__destroy']);
            }
        }
        else
        {
            $coId = Coroutine::getuid();
            $contextMap = &static::$contextMap;
            if(isset($contextMap[$coId]))
            {
                $context = $contextMap[$coId];
            }
            else
            {
                $context = $contextMap[$coId] = new \Swoole\Coroutine\Context;
            }
        }
        foreach($data as $k => $v)
        {
            $context[$k] = $v;
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
        $context = Coroutine::getContext();
        if($context)
        {
            if(!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([static::class, '__destroy']);
            }
        }
        else
        {
            $coId = Coroutine::getuid();
            $contextMap = &static::$contextMap;
            if(isset($contextMap[$coId]))
            {
                $context = $contextMap[$coId];
            }
            else
            {
                $context = $contextMap[$coId] = new \Swoole\Coroutine\Context;
            }
        }
        $result = $callback($context);
        return $result;
    }

    /**
     * 获取当前上下文
     * @return array
     */
    public static function getContext()
    {
        $context = Coroutine::getContext();
        if($context)
        {
            if(!($context['__bindDestroy'] ?? false))
            {
                $context['__bindDestroy'] = true;
                Coroutine::defer([static::class, '__destroy']);
            }
        }
        else
        {
            $coId = Coroutine::getuid();
            $contextMap = &static::$contextMap;
            if(isset($contextMap[$coId]))
            {
                $context = $contextMap[$coId];
            }
            else
            {
                $contextMap[$coId] = new \Swoole\Coroutine\Context;
            }
        }
        return $context;
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
        return static::get('server')->getBean($name, ...$params);
    }

    /**
     * 在当前请求上下文中获取Bean对象
     * @param string $name
     * @return mixed
     */
    public static function getBean($name, ...$params)
    {
        $context = static::getContext();
        if(isset($context['container']))
        {
            $container = $context['container'];
        }
        else
        {
            if(isset($context['server']))
            {
                $container = $context['server']->getContainer()->newSubContainer();
            }
            else
            {
                $container = App::getContainer()->newSubContainer();
            }
            $context['container'] = $container;
        }
        return $container->get($name, ...$params);
    }

}
