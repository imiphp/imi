<?php

namespace Imi\Server\WebSocket\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\ConnectContext;
use Imi\Log\Log;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Route\Annotation\WebSocket\WSRoute as WSRouteAnnotation;
use Imi\Server\Route\RouteCallable;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;

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
    protected $rules = [];

    /**
     * @ServerInject("HttpRoute")
     *
     * @var \Imi\Server\Http\Route\HttpRoute
     */
    protected $httpRoute;

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return \Imi\Server\WebSocket\Route\RouteResult
     */
    public function parse($data)
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
    public function addRuleAnnotation(WSRouteAnnotation $annotation, $callable, $options = [])
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
    public function existsRule(WSRouteAnnotation $rule)
    {
        return isset($this->rules[spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则.
     *
     * @return \Imi\Server\WebSocket\Route\RouteItem[]
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
    private function checkCondition($data, WSRouteAnnotation $annotation)
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
        $annotation = $routeItem->annotation;
        $route = (Text::isEmpty($annotation->route) ? '' : ('url=' . $annotation->route . ', ')) . 'condition=' . json_encode($annotation->condition, \JSON_UNESCAPED_UNICODE);
        if ($callable instanceof RouteCallable)
        {
            $logString = sprintf('WebSocket Route %s duplicated (%s::%s)', $route, $callable->className, $callable->methodName);
        }
        elseif (\is_array($callable))
        {
            $class = BeanFactory::getObjectClass($callable[0]);
            $method = $callable[1];
            $logString = sprintf('WebSocket Route "%s" duplicated (%s::%s)', $route, $class, $method);
        }
        else
        {
            $logString = sprintf('WebSocket Route "%s" duplicated', $route);
        }
        Log::warning($logString);
    }
}
