<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Server\Contract\IServer;
use Imi\Server\ServerManager;

class DelayServerBeanCallable
{
    private IServer $server;

    private string $beanName;

    private string $methodName;

    private array $constructArgs;

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

    /**
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return $this->server->getBean($this->beanName, ...$this->constructArgs)->{$this->methodName}(...$args);
    }
}
