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
     *
     * @var string|null
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
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = RequestContext::get('response');
        if('OPTIONS' === $request->getMethod())
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
        if(null === $this->allowOrigin)
        {
            $response = $response->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('Origin'));
        }
        else if($this->allowOrigin)
        {
            $response = $response->withHeader('Access-Control-Allow-Origin', $this->allowOrigin);
        }
        if(null !== $this->allowCredentials)
        {
            $response = $response->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials);
        }
        RequestContext::set('response', $response);
        return $handler->handle($request);
    }

}