<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;
use Imi\Server\Contract\IServer;
use Imi\Server\ServerManager;

class DelayServerBeanCallable
{
    private ?IServer $server = null;

    private string $beanName = '';

    private string $methodName = '';

    private array $constructArgs = [];

    private ?bool $returnsReference = null;

    /**
     * @param string|\Imi\Server\Contract\IServer $server
     */
    public function __construct($server, string $beanName, string $methodName, array $constructArgs = [])
    {
        if (\is_string($server))
        {
            $server = ServerManager::getServer($server);
        }
        $this->server = $server;
        $this->beanName = $beanName;
        $this->methodName = $methodName;
        $this->constructArgs = $constructArgs;
    }

    public function getBeanName(): string
    {
        return $this->beanName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getServer(): IServer
    {
        return $this->server;
    }

    public function getInstance(): object
    {
        return $this->server->getBean($this->beanName, ...$this->constructArgs);
    }

    public function returnsReference(): bool
    {
        return $this->returnsReference ??= ReflectionContainer::getMethodReflection(BeanFactory::getObjectClass($this->getInstance()), $this->methodName)->returnsReference();
    }

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        if ($this->returnsReference())
        {
            return $this->getInstance()->{$this->methodName}(...$args);
        }
        else
        {
            $result = $this->getInstance()->{$this->methodName}(...$args);

            return $result;
        }
    }
}
