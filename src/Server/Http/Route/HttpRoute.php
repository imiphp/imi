<?php

namespace Imi\Server\Http\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Log\Log;
use Imi\Server\Http\Message\Request;
use Imi\Server\Route\Annotation\Route as RouteAnnotation;
use Imi\Server\Route\RouteCallable;
use Imi\Server\View\Parser\ViewParser;
use Imi\Util\Imi;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Uri;

/**
 * @Bean("HttpRoute")
 */
class HttpRoute
{
    /**
     * 路由规则.
     *
     * @var \Imi\Server\Http\Route\RouteItem[][]
     */
    protected $rules = [];

    /**
     * url规则缓存.
     *
     * @var array
     */
    private $rulesCache = [];

    /**
     * 检查URL是否匹配的缓存.
     *
     * @var \Imi\Server\Http\Route\UrlCheckResult[][]
     */
    private $urlCheckCache = [];

    /**
     * url匹配缓存数量.
     *
     * @var int
     */
    private $urlCheckCacheCount = 0;

    /**
     * URL是否匹配的缓存数量.
     *
     * @var int
     */
    protected $urlCacheNumber = 1024;

    /**
     * 忽略 URL 规则大小写.
     *
     * @var bool
     */
    protected $ignoreCase = false;

    /**
     * 智能尾部斜杠，无论是否存在都匹配.
     *
     * @var bool
     */
    protected $autoEndSlash = false;

    /**
     * 增加路由规则.
     *
     * @param string                             $url        url规则
     * @param mixed                              $callable   回调
     * @param \Imi\Server\Route\Annotation\Route $annotation 路由定义注解，可选
     *
     * @return void
     */
    public function addRule(string $url, $callable, RouteAnnotation $annotation = null)
    {
        if (null === $annotation)
        {
            $annotation = new \Imi\Server\Route\Annotation\Route([
                'url' => $url,
            ]);
        }
        $this->rules[$url][spl_object_hash($annotation)] = new RouteItem($annotation, $callable, ViewParser::getInstance()->getByCallable($callable));
    }

    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param \Imi\Server\Route\Annotation\Route $annotation
     * @param mixed                              $callable
     * @param array                              $options
     *
     * @return void
     */
    public function addRuleAnnotation(RouteAnnotation $annotation, $callable, $options = [])
    {
        $routeItem = new RouteItem($annotation, $callable, ViewParser::getInstance()->getByCallable($callable), $options);
        if (isset($options['middlewares']))
        {
            $routeItem->middlewares = $options['middlewares'];
        }
        if (isset($options['wsConfig']))
        {
            $routeItem->wsConfig = $options['wsConfig'];
        }
        if (isset($options['singleton']))
        {
            $routeItem->singleton = $options['singleton'];
        }
        $this->rules[$annotation->url][spl_object_hash($annotation)] = $routeItem;
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
     * @param \Imi\Server\Route\Annotation\Route $rule
     *
     * @return bool
     */
    public function existsRule(RouteAnnotation $rule)
    {
        return isset($this->rules[$rule->url][spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 路由解析处理.
     *
     * @param Request $request
     *
     * @return \Imi\Server\Http\Route\RouteResult|null
     */
    public function parse(Request $request)
    {
        $pathInfo = $request->getUri()->getPath();
        $thisRules = &$this->rules;
        if (isset($thisRules[$pathInfo]))
        {
            $rules = [$pathInfo => $thisRules[$pathInfo]];
        }
        else
        {
            $rules = [];
        }
        $ignoreCase = $this->ignoreCase;
        for ($i = 0; $i < 2; ++$i)
        {
            if ($rules)
            {
                /** @var \Imi\Server\Http\Route\RouteItem[] $items */
                foreach ($rules as $urlRule => $items)
                {
                    $result = $this->checkUrl($urlRule, $pathInfo);
                    $resultResult = $result->result;
                    $resultParams = $result->params;
                    if ($resultResult || $result->resultIgnoreCase)
                    {
                        foreach ($items as $item)
                        {
                            $itemAnnotation = $item->annotation;
                            if (
                            ($resultResult || ($ignoreCase || $itemAnnotation->ignoreCase)) &&
                            $this->checkMethod($request, $itemAnnotation->method) &&
                            $this->checkDomain($request, $itemAnnotation->domain, $domainParams) &&
                            $this->checkParamsGet($request, $itemAnnotation->paramsGet) &&
                            $this->checkParamsPost($request, $itemAnnotation->paramsPost) &&
                            $this->checkParamsBody($request, $itemAnnotation->paramsBody, $itemAnnotation->paramsBodyMultiLevel) &&
                            $this->checkHeader($request, $itemAnnotation->header) &&
                            $this->checkRequestMime($request, $itemAnnotation->requestMime)
                        ) {
                                if ([] === $domainParams)
                                {
                                    $params = $resultParams;
                                }
                                else
                                {
                                    $params = array_merge($resultParams, $domainParams);
                                }

                                return new RouteResult(clone $item, $result, $params);
                            }
                        }
                    }
                }
            }
            $rules = $thisRules;
        }

        return null;
    }

    /**
     * 检查验证url是否匹配.
     *
     * @param string $urlRule
     * @param string $pathInfo
     *
     * @return \Imi\Server\Http\Route\UrlCheckResult
     */
    public function checkUrl(string $urlRule, string $pathInfo)
    {
        $urlCheckCache = &$this->urlCheckCache;
        if (!isset($urlCheckCache[$pathInfo][$urlRule]))
        {
            $urlCheckCacheCount = &$this->urlCheckCacheCount;
            $rule = $this->parseRule($urlRule, $fields, $isRegular);
            $params = [];
            if ($isRegular)
            {
                if ($matchResult = preg_match_all($rule, $pathInfo, $matches) > 0)
                {
                    foreach ($fields as $i => $fieldName)
                    {
                        $params[$fieldName] = $matches[$i + 1][0];
                    }
                }
            }
            else
            {
                $matchResult = $rule === $pathInfo;
            }
            $result = new UrlCheckResult($matchResult, $params);
            if (!$matchResult)
            {
                if ($isRegular)
                {
                    // 正则加i忽略大小写
                    if (preg_match_all($rule . 'i', $pathInfo, $matches) > 0)
                    {
                        foreach ($fields as $i => $fieldName)
                        {
                            $params[$fieldName] = $matches[$i + 1][0];
                        }
                        $result->resultIgnoreCase = true;
                        $result->params = $params;
                    }
                }
                elseif (0 === strcasecmp($rule, $pathInfo))
                {
                    $result->resultIgnoreCase = true;
                }
            }
            // 最大缓存数量处理
            if ($urlCheckCacheCount >= $this->urlCacheNumber)
            {
                array_shift($urlCheckCache);
                --$urlCheckCacheCount;
            }
            $urlCheckCache[$pathInfo][$urlRule] = $result;
            ++$urlCheckCacheCount;
        }

        return $urlCheckCache[$pathInfo][$urlRule];
    }

    /**
     * 处理规则为正则.
     *
     * @param string $rule
     * @param array  $fields    规则中包含的自定义参数
     * @param bool   $isRegular 是否为正则
     *
     * @return string
     */
    private function parseRule($rule, &$fields, &$isRegular)
    {
        $rulesCache = &$this->rulesCache;
        if (isset($rulesCache[$rule]))
        {
            $cache = $rulesCache[$rule];
            $fields = $cache['fields'];
            $isRegular = $cache['isRegular'];

            return $cache['pattern'];
        }
        else
        {
            $fields = [];
            if (false === strpos($rule, '/'))
            {
                $parsedRule = $rule;
            }
            else
            {
                $parsedRule = str_replace('/', '\/', $rule);
            }
            $pattern = preg_replace_callback(
                '/\{(([^\}:]+?)|([^:]+?):(?:([^{}]*(?:\{(?-1)\}[^{}]*)*))?)\}/',
                function ($matches) use (&$fields) {
                    if (isset($matches[4]))
                    {
                        // 正则
                        $fields[] = $matches[3];

                        return '(' . $matches[4] . ')';
                    }
                    else
                    {
                        // 正常匹配
                        $fields[] = $matches[1];

                        return '(.+)';
                    }
                },
                $parsedRule, -1, $isRegular
            );
            $isRegular = $isRegular > 0;
            if ($isRegular)
            {
                $pattern = '/^' . $pattern . '\/?$/';
            }
            else
            {
                $pattern = $rule;
            }
            $rulesCache[$rule] = [
                'pattern'   => $pattern,
                'fields'    => $fields,
                'isRegular' => $isRegular,
            ];

            return $pattern;
        }
    }

    /**
     * 检查验证请求方法是否匹配.
     *
     * @param Request $request
     * @param mixed   $method
     *
     * @return bool
     */
    private function checkMethod(Request $request, $method)
    {
        if (null === $method)
        {
            return true;
        }
        elseif (\is_array($method))
        {
            return \in_array($request->getMethod(), $method);
        }
        else
        {
            return $method === $request->getMethod();
        }
    }

    /**
     * 检查验证域名是否匹配.
     *
     * @param Request    $request
     * @param mixed      $domain
     * @param array|null $params
     *
     * @return bool
     */
    private function checkDomain(Request $request, $domain, &$params)
    {
        $params = [];
        if (null === $domain)
        {
            return true;
        }
        if (!\is_array($domain))
        {
            $domain = [$domain];
        }
        $uriDomain = Uri::getDomain($request->getUri());
        foreach ($domain as $rule)
        {
            $rule = $this->parseRule($rule, $fields, $isRegular);
            if ($isRegular)
            {
                // 域名匹配不区分大小写
                if (preg_match_all($rule . 'i', $uriDomain, $matches) > 0)
                {
                    $params = [];
                    foreach ($fields as $i => $fieldName)
                    {
                        $params[$fieldName] = $matches[$i + 1][0];
                    }

                    return true;
                }
            }
            elseif (0 === strcasecmp($rule, $uriDomain))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查验证GET参数是否匹配.
     *
     * @param Request $request
     * @param mixed   $params
     *
     * @return bool
     */
    private function checkParamsGet(Request $request, $params)
    {
        if (null === $params)
        {
            return true;
        }

        return Imi::checkCompareRules($params, function ($name) use ($request) {
            return $request->get($name);
        });
    }

    /**
     * 检查验证POST参数是否匹配.
     *
     * @param Request $request
     * @param mixed   $params
     *
     * @return bool
     */
    private function checkParamsPost(Request $request, $params)
    {
        if (null === $params)
        {
            return true;
        }

        return Imi::checkCompareRules($params, function ($name) use ($request) {
            return $request->post($name);
        });
    }

    /**
     * 检查验证 JSON、XML 参数是否匹配.
     *
     * @param Request $request
     * @param mixed   $params
     * @param bool    $paramsBodyMultiLevel
     *
     * @return bool
     */
    private function checkParamsBody(Request $request, $params, bool $paramsBodyMultiLevel)
    {
        if (null === $params)
        {
            return true;
        }

        $parsedBody = $request->getParsedBody();
        $isObject = \is_object($parsedBody);

        return Imi::checkCompareRules($params, function ($name) use ($parsedBody, $isObject, $paramsBodyMultiLevel) {
            if ($paramsBodyMultiLevel)
            {
                return ObjectArrayHelper::get($parsedBody, $name);
            }
            elseif ($isObject)
            {
                return $parsedBody->$name ?? null;
            }
            else
            {
                return $parsedBody[$name] ?? null;
            }
        });
    }

    /**
     * 检查验证请求头是否匹配.
     *
     * @param Request $request
     * @param mixed   $header
     *
     * @return bool
     */
    private function checkHeader(Request $request, $header)
    {
        if (null === $header)
        {
            return true;
        }

        return Imi::checkCompareRules($header, function ($name) use ($request) {
            return $request->getHeaderLine($name);
        });
    }

    /**
     * 检查验证请求媒体类型是否匹配.
     *
     * @param Request $request
     * @param mixed   $requestMime
     *
     * @return bool
     */
    private function checkRequestMime(Request $request, $requestMime)
    {
        if (null === $requestMime)
        {
            return true;
        }

        return Imi::checkCompareValues($requestMime, $request->getHeaderLine('Content-Type'));
    }

    /**
     * 获取当前缓存的url匹配数量.
     *
     * @return int
     */
    public function getUrlCacheNumber()
    {
        return $this->urlCacheNumber;
    }

    /**
     * Get 忽略 URL 规则大小写.
     *
     * @return bool
     */
    public function getIgnoreCase(): bool
    {
        return $this->ignoreCase;
    }

    /**
     * Get 智能尾部斜杠，无论是否存在都匹配.
     *
     * @return bool
     */
    public function getAutoEndSlash(): bool
    {
        return $this->autoEndSlash;
    }

    /**
     * 检查重复路由.
     *
     * @return void
     */
    public function checkDuplicateRoutes()
    {
        foreach ($this->rules as $rules)
        {
            $first = true;
            $map = [];
            foreach ($rules as $routeItem)
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
    }

    /**
     * @param RouteItem $routeItem
     *
     * @return void
     */
    private function logDuplicated(RouteItem $routeItem)
    {
        $callable = $routeItem->callable;
        if ($callable instanceof RouteCallable)
        {
            $logString = sprintf('Route "%s" duplicated (%s::%s)', $routeItem->annotation->url, $callable->className, $callable->methodName);
        }
        elseif (\is_array($callable))
        {
            $class = BeanFactory::getObjectClass($callable[0]);
            $method = $callable[1];
            $logString = sprintf('Route "%s" duplicated (%s::%s)', $routeItem->annotation->url, $class, $method);
        }
        else
        {
            $logString = sprintf('Route "%s" duplicated', $routeItem->annotation->url);
        }
        Log::warning($logString);
    }
}
