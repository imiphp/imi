<?php

namespace Imi\Server\TcpServer\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Route\Annotation\Tcp\TcpRoute as TcpRouteAnnotation;
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
    protected array $rules = [];

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return RouteResult|null
     */
    public function parse($data): ?RouteResult
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
    public function addRuleAnnotation(TcpRouteAnnotation $annotation, $callable, array $options = [])
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
    public function existsRule(TcpRouteAnnotation $rule): bool
    {
        return isset($this->rules[spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则.
     *
     * @return \Imi\Server\TcpServer\Route\RouteItem[]
     */
    public function getRules(): array
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
    private function checkCondition($data, TcpRouteAnnotation $annotation): bool
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
}
