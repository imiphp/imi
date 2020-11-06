<?php

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
     *
     * @var int
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
     *
     * @param \Imi\Server\Http\Message\Request $request
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middlewares = &$this->middlewares;
        $index = $this->index;
        if (isset($middlewares[$index]))
        {
            $middleware = $middlewares[$index];
            if (\is_object($middleware))
            {
                $requestHandler = $middleware;
            }
            else
            {
                $requestHandler = RequestContext::getServerBean($middleware);
            }
        }
        else
        {
            return RequestContext::get('response');
        }

        return $requestHandler->process($request, $this->next());
    }

    /**
     * 获取下一个RequestHandler对象
     *
     * @return static
     */
    protected function next(): self
    {
        ++$this->index;

        return $this;
    }

    /**
     * 是否是最后一个.
     *
     * @return bool
     */
    public function isLast(): bool
    {
        return !isset($this->middlewares[$this->index]);
    }
}
