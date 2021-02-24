<?php

declare(strict_types=1);

namespace Imi\Server\Route;

use Imi\Server\ServerManager;

class RouteCallable
{
    /**
     * 服务器名.
     *
     * @var string
     */
    public string $serverName = '';

    /**
     * 类名.
     *
     * @var string
     */
    public string $className = '';

    /**
     * 方法名.
     *
     * @var string
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
     *
     * @param array $params
     *
     * @return callable
     */
    public function getCallable(array $params = []): callable
    {
        $className = $this->className;
        $methodName = $this->methodName;
        foreach ($params as $name => $value)
        {
            $search = '{$' . $name . '}';
            if (false !== strpos($className, $search))
            {
                $className = str_replace($search, $value, $className);
            }
            if (false !== strpos($methodName, $search))
            {
                $methodName = str_replace($search, $value, $methodName);
            }
        }

        return [ServerManager::getServer($this->serverName)->getBean($className), $methodName];
    }
}
