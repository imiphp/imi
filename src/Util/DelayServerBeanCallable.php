<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;
use Imi\Server\Contract\IServer;
use Imi\Server\ServerManager;

class DelayServerBeanCallable
{
    private string $server = '';

    private ?IServer $serverInstance = null;

    private ?bool $returnsReference = null;

    private ?object $instance = null;

    /**
     * @param string|IServer $server
     */
    public function __construct($server, private readonly string $beanName, private readonly string $methodName, private readonly array $constructArgs = [])
    {
        if (\is_string($server))
        {
            $this->server = $server;
            $this->serverInstance = ServerManager::getServer($server);
        }
        else
        {
            $this->server = $server->getName();
            $this->serverInstance = $server;
        }
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
        return $this->serverInstance ?? ServerManager::getServer($this->server);
    }

    public function getInstance(): object
    {
        return $this->instance ??= $this->getServer()->getBean($this->beanName, ...$this->constructArgs);
    }

    public function returnsReference(): bool
    {
        return $this->returnsReference ??= ReflectionContainer::getMethodReflection(BeanFactory::getObjectClass($this->getInstance()), $this->methodName)->returnsReference();
    }

    /**
     * @return mixed
     */
    public function &__invoke(mixed ...$args)
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

    public function __serialize(): array
    {
        return [
            'server'           => $this->server,
            'beanName'         => $this->beanName,
            'methodName'       => $this->methodName,
            'constructArgs'    => $this->constructArgs,
        ];
    }

    public function __unserialize(array $data): void
    {
        ['server' => $this->server, 'beanName' => $this->beanName, 'methodName' => $this->methodName, 'constructArgs' => $this->constructArgs] = $data;
    }
}
