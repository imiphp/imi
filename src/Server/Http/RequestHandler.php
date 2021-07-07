<?php

declare(strict_types=1);

namespace Imi\Server\Http;

use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * 中间件数组.
     *
     * @var string[]
     */
    protected array $middlewares = [];

    /**
     * 当前执行第几个.
     */
    protected int $index = 0;

    /**
     * 构造方法.
     *
     * @param string[] $middlewares 中间件数组
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middlewares = &$this->middlewares;
        $index = &$this->index;
        if (isset($middlewares[$index]))
        {
            /** @var \Psr\Http\Server\MiddlewareInterface $requestHandler */
            $requestHandler = RequestContext::getServerBean($middlewares[$index++]);

            return $requestHandler->process($request, $this);
        }
        else
        {
            return RequestContext::get('response');
        }
    }
}
