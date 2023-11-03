<?php

declare(strict_types=1);

namespace Imi\Server\Contract;

use Imi\App;
use Imi\Bean\Container;
use Imi\Event\TEvent;
use Imi\RequestContext;

/**
 * 服务器基类.
 */
abstract class BaseServer implements IServer
{
    use TEvent;

    /**
     * 容器.
     */
    protected Container $container;

    /**
     * 构造方法.
     */
    public function __construct(
        /**
         * 服务器名称.
         */
        protected string $name,
        /**
         * 服务器配置.
         */
        protected array $config)
    {
        $this->container = $container = App::getContainer()->newSubContainer();
        $beans = $config['beans'] ?? [];
        if ($beans)
        {
            $container->appendBinds($beans);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function getBean(string $name, mixed ...$params): mixed
    {
        $context = RequestContext::getContext();
        $server = $context['server'] ?? null;
        $context['server'] = $this;
        try
        {
            return $this->container->get($name, ...$params);
        }
        finally
        {
            if ($server)
            {
                $context['server'] = $server;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function callServerMethod(string $methodName, mixed ...$args): mixed
    {
        return null;
    }
}
