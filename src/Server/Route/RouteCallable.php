<?php

declare(strict_types=1);

namespace Imi\Server\Route;

use Imi\Server\Base;

class RouteCallable
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * 类名.
     *
     * @var string
     */
    public string $className;

    /**
     * 方法名.
     *
     * @var string
     */
    public string $methodName;

    public function __construct(Base $server, string $className, string $methodName)
    {
        $this->server = $server;
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

        return [$this->server->getBean($className), $methodName];
    }
}
