<?php

namespace Imi\Server\TcpServer\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Log\Log;
use Imi\Server\Route\Annotation\Tcp\TcpRoute as TcpRouteAnnotation;
use Imi\Server\Route\RouteCallable;
use Imi\Util\ObjectArrayHelper;

/**
 * @Bean("TcpRoute")
 */
class TcpRoute implements IRoute
{
    /**
     * 路由规则.
     *
     * @var \Imi\Server\TcpServer\Route\RouteItem[]
     */
    protected $rules = [];

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function parse($data)
    {
        foreach ($this->rules as $item)
        {
            if ($this->checkCondition($data, $item->annotation))
            {
                return new RouteResult($item);
            }
        }

        return null;
    }

    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param Imi\Server\Route\Annotation\TcpServer\TcpRoute $annotation
     * @param mixed                                          $callable
     * @param array                                          $options
     *
     * @return void
     */
    public function addRuleAnnotation(TcpRouteAnnotation $annotation, $callable, $options = [])
    {
        $routeItem = new RouteItem($annotation, $callable, $options);
        if (isset($options['middlewares']))
        {
            $routeItem->middlewares = $options['middlewares'];
        }
        if (isset($options['singleton']))
        {
            $routeItem->singleton = $options['singleton'];
        }
        $this->rules[spl_object_hash($annotation)] = $routeItem;
    }

    /**
     * 清空路由规则.
     *
     * @return void
     */
    public function clearRules()
    {
        $this->rules = [];
    }

    /**
     * 路由规则是否存在.
     *
     * @param Imi\Server\Route\Annotation\TcpServer\TcpRoute $rule
     *
     * @return bool
     */
    public function existsRule(TcpRouteAnnotation $rule)
    {
        return isset($this->rules[spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则.
     *
     * @return \Imi\Server\TcpServer\Route\RouteItem[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 检查条件是否匹配.
     *
     * @param array|object      $data
     * @param WSRouteAnnotation $annotation
     *
     * @return bool
     */
    private function checkCondition($data, TcpRouteAnnotation $annotation)
    {
        if ([] === $annotation->condition)
        {
            return false;
        }
        foreach ($annotation->condition as $name => $value)
        {
            if (ObjectArrayHelper::get($data, $name) !== $value)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * 检查重复路由.
     *
     * @return void
     */
    public function checkDuplicateRoutes()
    {
        $first = true;
        $map = [];
        foreach ($this->rules as $routeItem)
        {
            $string = (string) $routeItem->annotation;
            if (isset($map[$string]))
            {
                if ($first)
                {
                    $first = false;
                    $this->logDuplicated($map[$string]);
                }
                $this->logDuplicated($routeItem);
            }
            else
            {
                $map[$string] = $routeItem;
            }
        }
    }

    private function logDuplicated(RouteItem $routeItem)
    {
        $callable = $routeItem->callable;
        $route = 'condition=' . json_encode($routeItem->annotation->condition, \JSON_UNESCAPED_UNICODE);
        if ($callable instanceof RouteCallable)
        {
            $logString = sprintf('TCP Route %s duplicated (%s::%s)', $route, $callable->className, $callable->methodName);
        }
        elseif (\is_array($callable))
        {
            $class = BeanFactory::getObjectClass($callable[0]);
            $method = $callable[1];
            $logString = sprintf('TCP Route "%s" duplicated (%s::%s)', $route, $class, $method);
        }
        else
        {
            $logString = sprintf('TCP Route "%s" duplicated', $route);
        }
        Log::warning($logString);
    }
}
