<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer;

use Imi\RequestContext;
use Imi\Server\TcpServer\Message\IReceiveData;

class ReceiveHandler implements IReceiveHandler
{
    /**
     * 中间件数组.
     *
     * @var string[]|\Imi\Server\TcpServer\Middleware\IMiddleware[]
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
     * 返回值为响应内容，为null则无任何响应.
     *
     * @return mixed
     */
    public function handle(IReceiveData $data)
    {
        $middlewares = &$this->middlewares;
        $index = &$this->index;
        if (isset($middlewares[$index]))
        {
            $middleware = $middlewares[$index++];
            if (\is_object($middleware))
            {
                $requestHandler = $middleware;
            }
            else
            {
                $requestHandler = RequestContext::getServerBean($middleware);
            }

            return $requestHandler->process($data, $this);
        }
        else
        {
            return null;
        }
    }
}
