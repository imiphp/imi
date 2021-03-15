<?php

declare(strict_types=1);

namespace Imi\Server\Contract;

use Imi\App;
use Imi\Bean\Container;
use Imi\Event\TEvent;

/**
 * 服务器基类.
 */
abstract class BaseServer implements IServer
{
    use TEvent;

    /**
     * 服务器名称.
     *
     * @var string
     */
    protected string $name = '';

    /**
     * 服务器配置.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * 容器.
     *
     * @var \Imi\Bean\Container
     */
    protected Container $container;

    /**
     * 构造方法.
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct(string $name, array $config)
    {
        $this->container = App::getContainer()->newSubContainer();
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * 获取服务器名称.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取配置信息.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * 获取容器对象
     *
     * @return \Imi\Bean\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * 获取Bean对象
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return object
     */
    public function getBean(string $name, ...$params): object
    {
        return $this->container->get($name, ...$params);
    }

    /**
     * 调用服务器方法.
     *
     * @param string $methodName
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function callServerMethod(string $methodName, ...$args)
    {
        return null;
    }
}
