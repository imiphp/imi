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
     */
    private static IContextManager $contextManager;

    private function __construct()
    {
    }

    /**
     * 获取上下文管理器实例.
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
     */
    public static function getCurrentFlag(): string
    {
        return static::getInstance()->getCurrentFlag();
    }

    /**
     * 为当前请求创建上下文，返回当前协程ID.
     */
    public static function create(array $data = []): ArrayObject
    {
        $instance = static::getInstance();

        return $instance->create($instance->getCurrentFlag(), $data);
    }

    /**
     * 销毁上下文.
     */
    public static function destroy(?string $flag = null): bool
    {
        $instance = static::getInstance();

        return $instance->destroy($flag ?? $instance->getCurrentFlag());
    }

    /**
     * 上下文是否存在.
     */
    public static function exists(string $flag): bool
    {
        return static::getInstance()->exists($flag);
    }

    /**
     * 获取上下文数据.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);

        return $context[$name] ?? $default;
    }

    /**
     * 设置上下文数据.
     *
     * @param mixed $value
     */
    public static function set(string $name, $value): void
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);
        $context[$name] = $value;
    }

    /**
     * 批量设置上下文数据.
     */
    public static function muiltiSet(array $data): void
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);
        foreach ($data as $k => $v)
        {
            $context[$k] = $v;
        }
    }

    /**
     * 使用回调来使用当前请求上下文数据.
     *
     * @return mixed
     */
    public static function use(callable $callback)
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);
        $result = $callback($context);

        return $result;
    }

    /**
     * 获取当前上下文.
     */
    public static function getContext(): ArrayObject
    {
        $instance = static::getInstance();

        return $instance->get($instance->getCurrentFlag(), true);
    }

    /**
     * 获取当前的服务器对象
     */
    public static function getServer(): ?IServer
    {
        return static::get('server');
    }

    /**
     * 在当前服务器上下文中获取Bean对象
     *
     * @param array $params
     */
    public static function getServerBean(string $name, ...$params): object
    {
        return static::get('server')->getBean($name, ...$params);
    }

    /**
     * 获取请求上下文容器.
     */
    public static function getContainer(): Container
    {
        return self::getContext()['container'];
    }

    /**
     * 在当前请求上下文中获取Bean对象
     *
     * @template T
     *
     * @param class-string<T>|string $name
     * @param mixed           ...$params
     *
     * @return T
     */
    public static function getBean(string $name, ...$params)
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);
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
