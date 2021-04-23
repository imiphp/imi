<?php

namespace Imi\Server\Route;

class RouteCallable
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 类名.
     *
     * @var string
     */
    public $className;

    /**
     * 方法名.
     *
     * @var string
     */
    public $methodName;

    /**
     * @param \Imi\Server\Base $server
     * @param string           $className
     * @param string           $methodName
     */
    public function __construct($server, $className, $methodName)
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
    public function getCallable($params = [])
    {
        $className = $this->className;
        $methodName = $this->methodName;
        if ($params)
        {
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
        }

        return [$this->server->getBean($className), $methodName];
    }
}
