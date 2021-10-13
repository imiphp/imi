<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer;

use Imi\RequestContext;
use Imi\Server\UdpServer\Message\IPacketData;

class PacketHandler implements IPacketHandler
{
    /**
     * 中间件数组.
     *
     * @var string[]|\Imi\Server\UdpServer\Middleware\IMiddleware[]
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
     * {@inheritDoc}
     */
    public function handle(IPacketData $data)
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
