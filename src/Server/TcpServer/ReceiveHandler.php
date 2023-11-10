<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer;

use Imi\RequestContext;
use Imi\Server\TcpServer\Message\IReceiveData;

class ReceiveHandler implements IReceiveHandler
{
    /**
     * 当前执行第几个.
     */
    protected int $index = 0;

    /**
     * 构造方法.
     */
    public function __construct(
        /**
         * @var array<string|object> 中间件数组
         */
        protected array $middlewares
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(IReceiveData $data): mixed
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
