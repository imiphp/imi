<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket;

use Imi\RequestContext;
use Imi\Server\WebSocket\Message\IFrame;

class MessageHandler implements IMessageHandler
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
    public function handle(IFrame $frame): mixed
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

            return $requestHandler->process($frame, $this);
        }
        else
        {
            return null;
        }
    }
}
