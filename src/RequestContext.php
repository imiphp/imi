<?php

declare(strict_types=1);

namespace Imi;

use Imi\Bean\Container;
use Imi\Core\Context\Contract\IContextManager;
use Imi\Core\Context\DefaultContextManager;
use Imi\Server\Contract\IServer;

class RequestContext
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 上下文管理器.
     */
    private static IContextManager $contextManager;

    /**
     * 获取上下文管理器实例.
     */
    public static function getInstance(): IContextManager
    {
        if (!isset(self::$contextManager))
        {
            $contextClass = Config::get('@app.imi.RequestContext', DefaultContextManager::class);

            return self::$contextManager = new $contextClass();
        }

        return self::$contextManager;
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
    public static function create(array $data = []): \ArrayObject
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
     */
    public static function get(string $name, mixed $default = null): mixed
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);

        return $context[$name] ?? $default;
    }

    /**
     * 设置上下文数据.
     */
    public static function set(string $name, mixed $value): void
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
     */
    public static function use(callable $callback): mixed
    {
        $instance = static::getInstance();
        $context = $instance->get($instance->getCurrentFlag(), true);

        return $callback($context);
    }

    /**
     * 获取一个闭包的值并将其持久化, 下次请求将直接从上下文中获取.
     */
    public static function remember(string $key, \Closure $closure): mixed
    {
        $ctx = self::getContext();

        return $ctx[$key] ??= $closure();
    }

    /**
     * 销毁一个上下文记住的值
     */
    public static function unset(string $key): void
    {
        $ctx = self::getContext();

        if (isset($ctx[$key]))
        {
            unset($ctx[$key]);
        }
    }

    /**
     * 获取当前上下文.
     */
    public static function getContext(): \ArrayObject
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
     */
    public static function getServerBean(string $name, mixed ...$params): object
    {
        return static::get('server')->getBean($name, ...$params);
    }

    /**
     * 获取请求上下文容器.
     */
    public static function getContainer(): Container
    {
        $context = self::getContext();
        if (isset($context['container']))
        {
            return $context['container'];
        }
        elseif (isset($context['server']))
        {
            return $context['container'] = $context['server']->getContainer()->newSubContainer();
        }
        else
        {
            return $context['container'] = App::getContainer()->newSubContainer();
        }
    }

    /**
     * 在当前请求上下文中获取Bean对象
     *
     * @template T
     *
     * @param class-string<T> $name
     *
     * @return T
     */
    public static function getBean(string $name, mixed ...$params): mixed
    {
        return self::getContainer()->get($name, ...$params);
    }

    /**
     * 获取Bean对象
     *
     * @template T
     *
     * @param class-string<T> $name
     *
     * @return T
     */
    public static function newInstance(string $name, mixed ...$params): mixed
    {
        return self::getContainer()->newInstance($name, ...$params);
    }
}
