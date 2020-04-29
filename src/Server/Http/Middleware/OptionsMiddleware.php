<?php
namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 解决：使用 application/json 请求时，浏览器会先发送一个 OPTIONS 请求
 * 
 * @Bean("OptionsMiddleware")
 */
class OptionsMiddleware implements MiddlewareInterface
{
    /**
     * 设置允许的 Origin
     * 为 null 时允许所有
     * 为数组时允许多个
     *
     * @var string|null|string[]
     */
    protected $allowOrigin = null;

    /**
     * 允许的请求头
     *
     * @var string
     */
    protected $allowHeaders = 'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie';

    /**
     * 允许的跨域请求头
     *
     * @var string
     */
    protected $exposeHeaders = 'Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie';

    /**
     * 允许的请求方法
     *
     * @var string
     */
    protected $allowMethods = 'GET, POST, PATCH, PUT, DELETE';

    /**
     * 是否允许跨域 Cookie
     *
     * @var string
     */
    protected $allowCredentials = 'true';

    /**
     * 当请求为 OPTIONS 时，是否中止后续中间件和路由逻辑
     *
     * 一般建议设为 true
     * 
     * @var bool
     */
    protected $optionsBreak = false;

    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestContext = RequestContext::getContext();
        $response = $requestContext['response'] ?? null;
        if($isOptions = ('OPTIONS' === $request->getMethod()))
        {
            if(null !== $this->allowHeaders)
            {
                $response = $response->withHeader('Access-Control-Allow-Headers', $this->allowHeaders);
            }
            if(null !== $this->exposeHeaders)
            {
                $response = $response->withHeader('Access-Control-Expose-Headers', $this->exposeHeaders);
            }
            if(null !== $this->allowMethods)
            {
                $response = $response->withHeader('Access-Control-Allow-Methods', $this->allowMethods);
            }
        }
        if(null === $this->allowOrigin || (is_array($this->allowOrigin) && in_array($request->getHeaderLine('Origin'), $this->allowOrigin)))
        {
            $response = $response->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('Origin'));
        }
        else if(!is_array($this->allowOrigin))
        {
            $response = $response->withHeader('Access-Control-Allow-Origin', $this->allowOrigin);
        }
        if(null !== $this->allowCredentials)
        {
            $response = $response->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials);
        }
        $requestContext['response'] = $response;
        if($isOptions && $this->optionsBreak)
        {
            return $response;
        }
        else
        {
            return $handler->handle($request);
        }
    }

}