<?php

namespace Imi\Server\WebSocket\Route;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\Route\Annotation\WebSocket\WSRoute as WSRouteAnnotation;
use Imi\Util\ObjectArrayHelper;

/**
 * @Bean("WSRoute")
 */
class WSRoute implements IRoute
{
    /**
     * 路由规则.
     *
     * @var \Imi\Server\WebSocket\Route\RouteItem[]
     */
    protected array $rules = [];

    /**
     * @ServerInject("HttpRoute")
     *
     * @var \Imi\Server\Http\Route\HttpRoute
     */
    protected HttpRoute $httpRoute;

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return RouteResult|null
     */
    public function parse($data): ?RouteResult
    {
        /** @var \Imi\Util\Uri $uri */
        $uri = ConnectContext::get('uri');
        $path = $uri->getPath();
        $httpRoute = $this->httpRoute;
        foreach ($this->rules as $item)
        {
            $itemAnnotation = $item->annotation;
            if ($this->checkCondition($data, $itemAnnotation)
            // http 路由匹配
            && (!$itemAnnotation->route || $httpRoute->checkUrl($itemAnnotation->route, $path)->result))
            {
                return new RouteResult($item);
            }
        }

        return null;
    }

    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param Imi\Server\Route\Annotation\WebSocket\WSRoute $annotation
     * @param mixed                                         $callable
     * @param array                                         $options
     *
     * @return void
     */
    public function addRuleAnnotation(WSRouteAnnotation $annotation, $callable, array $options = [])
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
     * @param Imi\Server\Route\Annotation\WebSocket\WSRoute $rule
     *
     * @return bool
     */
    public function existsRule(WSRouteAnnotation $rule): bool
    {
        return isset($this->rules[spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则.
     *
     * @return \Imi\Server\WebSocket\Route\RouteItem[]
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
    private function checkCondition($data, WSRouteAnnotation $annotation): bool
    {
        if ([] === $annotation->condition)
        {
            return false;
        }
        // 匹配 WebSocket 路由
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
