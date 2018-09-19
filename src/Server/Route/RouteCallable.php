<?php
namespace Imi\Server\Route;

use Imi\RequestContext;


class RouteCallable
{
    /**
     * 类名
     * @var string
     */
    public $className;

    /**
     * 方法名
     * @var string
     */
    public $methodName;

    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }

    /**
     * 获取可以被调用的格式
     * @param array $params
     * @return callable
     */
    public function getCallable($params = [])
    {
        $className = $this->className;
        $methodName = $this->methodName;
        foreach($params as $name => $value)
        {
            $className = str_replace('{$' . $name . '}', $value, $className);
            $methodName = str_replace('{$' . $name . '}', $value, $methodName);
        }
        return [RequestContext::getServer()->getBean($className), $methodName];
    }
}