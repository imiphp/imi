<?php
namespace Imi\Server\Http\Route;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Route\Annotation\Route as RouteAnnotation;
use Imi\Server\Route\RouteCallable;
use Imi\Util\Uri;

/**
 * @Bean("HttpRoute")
 */
class HttpRoute
{
    /**
     * 路由规则
     * @var \Imi\Server\Http\Route\RouteItem[][]
     */
    protected $rules = [];

    /**
     * url规则缓存
     * @var array
     */
    private $rulesCache = [];

    /**
     * 检查URL是否匹配的缓存
     * @var \Imi\Server\Http\Route\UrlCheckResult[][]
     */
    private $urlCheckCache = [];

    /**
     * url匹配缓存数量
     * @var integer
     */
    private $urlCheckCacheCount = 0;

    /**
     * URL是否匹配的缓存数量
     * @var integer
     */
    protected $urlCacheNumber = 1024;

    /**
     * 忽略 URL 规则大小写
     *
     * @var boolean
     */
    protected $ignoreCase = false;

    /**
     * 增加路由规则
     * @param string $url url规则
     * @param mixed $callable 回调
     * @param \Imi\Server\Route\Annotation\Route $annotation 路由定义注解，可选
     * @return void
     */
    public function addRule(string $url, $callable, \Imi\Server\Route\Annotation\Route $annotation = null)
    {
        if(null === $annotation)
        {
            $annotation = new \Imi\Server\Route\Annotation\Route([
                'url' => $url,
            ]);
        }
        $this->rules[$url][spl_object_hash($annotation)] = new RouteItem($annotation, $callable);
    }

    /**
     * 增加路由规则，直接使用注解方式
     * @param \Imi\Server\Route\Annotation\Route $annotation
     * @param mixed $callable
     * @param array $options
     * @return void
     */
    public function addRuleAnnotation(\Imi\Server\Route\Annotation\Route $annotation, $callable, $options = [])
    {
        $routeItem = new RouteItem($annotation, $callable, $options);
        if(isset($options['middlewares']))
        {
            $routeItem->middlewares = $options['middlewares'];
        }
        if(isset($options['wsConfig']))
        {
            $routeItem->wsConfig = $options['wsConfig'];
        }
        $this->rules[$annotation->url][spl_object_hash($annotation)] = $routeItem;
    }

    /**
     * 清空路由规则
     * @return void
     */
    public function clearRules()
    {
        $this->rules = [];
    }

    /**
     * 路由规则是否存在
     * @param \Imi\Server\Route\Annotation\Route $rule
     * @return boolean
     */
    public function existsRule(RouteAnnotation $rule)
    {
        return isset($this->rules[$rule->url][spl_object_hash($rule)]);
    }

    /**
     * 获取路由规则
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 路由解析处理
     * @param Request $request
     * @return \Imi\Server\Http\Route\RouteResult|null
     */
    public function parse(Request $request)
    {
        foreach($this->rules as $url => $items)
        {
            $result = $this->checkUrl($request, $url);
            if($result->result || $result->resultIgnoreCase)
            {
                foreach($items as $item)
                {
                    if(
                        ($result->result || ($this->ignoreCase || $item->annotation->ignoreCase)) &&
                        $this->checkMethod($request, $item->annotation->method) &&
                        $this->checkDomain($request, $item->annotation->domain, $domainParams) &&
                        $this->checkParamsGet($request, $item->annotation->paramsGet) &&
                        $this->checkParamsPost($request, $item->annotation->paramsPost) &&
                        $this->checkHeader($request, $item->annotation->header) &&
                        $this->checkRequestMime($request, $item->annotation->requestMime)
                    )
                    {
                        if([] === $domainParams)
                        {
                            $params = $result->params;
                        }
                        else
                        {
                            $params = array_merge($result->params, $domainParams);
                        }
                        return new RouteResult(clone $item, $result, $params);
                    }
                }
            }
        }
        return null;
    }

    /**
     * 检查验证url是否匹配
     * @param Request $request
     * @param string $url
     * @param array $params url路由中的自定义参数
     * @return \Imi\Server\Http\Route\UrlCheckResult
     */
    private function checkUrl(Request $request, string $url)
    {
        $pathInfo = $request->getServerParam('path_info');
        if(!isset($this->urlCheckCache[$pathInfo][$url]))
        {
            $rule = $this->parseRule($url, $fields);
            $params = [];
            if($result = preg_match_all($rule, $pathInfo, $matches) > 0)
            {
                foreach($fields as $i => $fieldName)
                {
                    $params[$fieldName] = $matches[$i + 1][0];
                }
            }
            $result = new UrlCheckResult($result, $params);
            if($result->result)
            {
                $result->resultIgnoreCase = true;
            }
            // 正则加i忽略大小写
            else if(preg_match_all($rule . 'i', $pathInfo, $matches) > 0)
            {
                foreach($fields as $i => $fieldName)
                {
                    $params[$fieldName] = $matches[$i + 1][0];
                }
                $result->resultIgnoreCase = true;
                $result->params = $params;
            }
            else
            {
                $result->resultIgnoreCase = false;
            }
            // 最大缓存数量处理
            if($this->urlCheckCacheCount >= $this->urlCacheNumber)
            {
                array_shift($this->urlCheckCache);
                --$this->urlCheckCacheCount;
            }
            $this->urlCheckCache[$pathInfo][$url] = $result;
            ++$this->urlCheckCacheCount;
        }
        return $this->urlCheckCache[$pathInfo][$url];
    }

    /**
     * 处理规则为正则
     * @param string $rule
     * @param array $fields 规则中包含的自定义参数
     * @return string
     */
    private function parseRule($rule, &$fields)
    {
        if(!isset($this->rulesCache[$rule]))
        {
            $fields = [];
            $rule = str_replace(['/', '\{', '\}'], ['\/', '{', '}'], preg_quote($rule));
            $this->rulesCache[$rule] = '/^' . preg_replace_callback(
                '/{([^}]+)}/i',
                function($matches)use(&$fields){
                    $fields[] = $matches[1];
                    return '(.+)';
                },
                $rule
            ) . '\/?$/';
        }
        return $this->rulesCache[$rule];
    }

    /**
     * 检查验证请求方法是否匹配
     * @param Request $request
     * @param mixed $method
     * @return boolean
     */
    private function checkMethod(Request $request, $method)
    {
        if(Text::isEmpty($method))
        {
            return true;
        }
        else if(is_array($method))
        {
            return in_array($request->getMethod(), $method);
        }
        else
        {
            return $method === $request->getMethod();
        }
    }
    
    /**
     * 检查验证域名是否匹配
     * @param Request $request
     * @param mixed $domain
     * @return boolean
     */
    private function checkDomain(Request $request, $domain, &$params)
    {
        $params = [];
        if(Text::isEmpty($domain))
        {
            return true;
        }
        if(!is_array($domain))
        {
            $domain = [$domain];
        }
        foreach($domain as $rule)
        {
            $rule = $this->parseRule($rule, $fields);
            if(preg_match_all($rule, Uri::getDomain($request->getUri()), $matches) > 0)
            {
                $params = [];
                foreach($fields as $i => $fieldName)
                {
                    $params[$fieldName] = $matches[$i + 1][0];
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * 检查验证GET参数是否匹配
     * @param Request $request
     * @param mixed $params
     * @return boolean
     */
    private function checkParamsGet(Request $request, $params)
    {
        if(empty($params))
        {
            return true;
        }
        return Imi::checkCompareRules($params, function($name) use($request){
            return $request->get($name);
        });
    }
    
    /**
     * 检查验证POST参数是否匹配
     * @param Request $request
     * @param mixed $params
     * @return boolean
     */
    private function checkParamsPost(Request $request, $params)
    {
        if(empty($params))
        {
            return true;
        }
        return Imi::checkCompareRules($params, function($name) use($request){
            return $request->post($name);
        });
    }

    /**
     * 检查验证请求头是否匹配
     * @param Request $request
     * @param mixed $header
     * @return boolean
     */
    private function checkHeader(Request $request, $header)
    {
        if(empty($header))
        {
            return true;
        }
        return Imi::checkCompareRules($header, function($name) use($request){
            return $request->getHeaderLine($name);
        });
    }
    
    /**
     * 检查验证请求媒体类型是否匹配
     * @param Request $request
     * @param mixed $requestMime
     * @return boolean
     */
    private function checkRequestMime(Request $request, $requestMime)
    {
        if(empty($requestMime))
        {
            return true;
        }
        return Imi::checkCompareValues($requestMime, $request->getHeaderLine('Content-Type'));
    }

    /**
     * 获取当前缓存的url匹配数量
     * @return int
     */
    public function getUrlCacheNumber()
    {
        return $this->urlCacheNumber;
    }
}