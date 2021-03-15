<?php

declare(strict_types=1);

namespace Imi;

use ArrayObject;
use Imi\Bean\Container;
use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\DefaultContextManager;
use Imi\Server\Contract\IServer;

class RequestContext
{
    /**
     * 上下文管理器.
     *
     * @var IContextManager
     */
    private static IContextManager $contextManager;

    private function __construct()
    {
    }

    /**
     * 获取上下文管理器实例.
     *
     * @return \Imi\Core\Context\Contract\IContextManager
     */
    public static function getInstance(): IContextManager
    {
        if (!isset(static::$contextManager))
        {
            $contextClass = Config::get('@app.imi.RequestContext', DefaultContextManager::class);

            return static::$contextManager = new $contextClass();
        }

        return static::$contextManager;
    }

    /**
     * 获取当前上下文标识.
     *
     * @return string
     */
    public static function getCurrentFlag(): string
    {
        return static::getInstance()->getCurrentFlag();
    }

    /**
     * 为当前请求创建上下文，返回当前协程ID.
     *
     * @param array $data
     *
     * @return \ArrayObject
     */
    public static function create(array $data = []): ArrayObject
    {
        return static::getInstance()->create(static::getCurrentFlag(), $data);
    }

    /**
     * 销毁上下文.
     *
     * @param string $flag
     *
     * @return bool
     */
    public static function destroy(string $flag): bool
    {
        return static::getInstance()->destroy($flag);
    }

    /**
     * 上下文是否存在.
     *
     * @param string $flag
     *
     * @return bool
     */
    public static function exists(string $flag): bool
    {
        return static::getInstance()->exists($flag);
    }

    /**
     * 获取上下文数据.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $context = static::getInstance()->get(static::getCurrentFlag(), true);

        return $context[$name] ?? $default;
    }

    /**
     * 设置上下文数据.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public static function set(string $name, $value): void
    {
        $context = static::getInstance()->get(static::getCurrentFlag(), true);
        $context[$name] = $value;
    }

    /**
     * 批量设置上下文数据.
     *
     * @param array $data
     *
     * @return void
     */
    public static function muiltiSet(array $data): void
    {
        $context = static::getInstance()->get(static::getCurrentFlag(), true);
        foreach ($data as $k => $v)
        {
            $context[$k] = $v;
        }
    }

    /**
     * 使用回调来使用当前请求上下文数据.
     *
     * @param callable $callback
     *
     * @return mixed
     */
    public static function use(callable $callback)
    {
        $context = static::getInstance()->get(static::getCurrentFlag(), true);
        $result = $callback($context);

        return $result;
    }

    /**
     * 获取当前上下文.
     *
     * @return ArrayObject
     */
    public static function getContext(): ArrayObject
    {
        return static::getInstance()->get(static::getCurrentFlag(), true);
    }

    /**
     * 获取当前的服务器对象
     *
     * @return IServer|null
     */
    public static function getServer(): ?IServer
    {
        return static::get('server');
    }

    /**
     * 在当前服务器上下文中获取Bean对象
     *
     * @param string $name
     * @param array  $params
     *
     * @return object
     */
    public static function getServerBean(string $name, ...$params): object
    {
        return static::get('server')->getBean($name, ...$params);
    }

    /**
     * 在当前请求上下文中获取Bean对象
     *
     * @param string $name
     * @param array  $params
     *
     * @return object
     */
    public static function getBean(string $name, ...$params): object
    {
        $context = static::getInstance()->get(static::getCurrentFlag(), true);
        if (isset($context['container']))
        {
            /** @var Container $container */
            $container = $context['container'];
        }
        else
        {
            if (isset($context['server']))
            {
                /** @var Container $container */
                $container = $context['container'] = $context['server']->getContainer()->newSubContainer();
            }
            else
            {
                $container = $context['container'] = App::getContainer()->newSubContainer();
            }
        }

        return $container->get($name, ...$params);
    }
}
