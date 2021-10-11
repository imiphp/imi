<?php

declare(strict_types=1);

namespace Imi\Server\Route;

use Imi\Server\ServerManager;

class RouteCallable
{
    /**
     * 服务器名.
     */
    public string $serverName = '';

    /**
     * 类名.
     */
    public string $className = '';

    /**
     * 方法名.
     */
    public string $methodName = '';

    public function __construct(string $serverName, string $className, string $methodName)
    {
        $this->serverName = $serverName;
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * 获取可以被调用的格式.
     */
    public function getCallable(array $params = []): callable
    {
        $className = $this->className;
        $methodName = $this->methodName;
        if ($params)
        {
            foreach ($params as $name => $value)
            {
                $search = '{$' . $name . '}';
                if (str_contains($className, $search))
                {
                    $className = str_replace($search, $value, $className);
                }
                if (str_contains($methodName, $search))
                {
                    $methodName = str_replace($search, $value, $methodName);
                }
            }
        }

        $server = ServerManager::getServer($this->serverName);

        return [$server->getBean($className, $server), $methodName];
    }
}
