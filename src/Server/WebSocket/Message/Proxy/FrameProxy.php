<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Message\Proxy;

use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @method        string|int                  getClientId()
 * @method static string|int                  getClientId()
 * @method        string                      getData()
 * @method static string                      getData()
 * @method        mixed                       getFormatData()
 * @method static mixed                       getFormatData()
 * @method        int                         getOpcode()
 * @method static int                         getOpcode()
 * @method        bool                        isFinish()
 * @method static bool                        isFinish()
 * @method        \Imi\Util\Socket\IPEndPoint getClientAddress()
 * @method static \Imi\Util\Socket\IPEndPoint getClientAddress()
 */
#[
    \Imi\RequestContextProxy\Annotation\RequestContextProxy(class: \Imi\Server\WebSocket\Message\IFrame::class, name: 'frame'),
    \Imi\Bean\Annotation\Bean(name: 'WebSocketFrameProxy')
]
class FrameProxy extends BaseRequestContextProxy implements \Imi\Server\WebSocket\Message\IFrame
{
    /**
     * {@inheritDoc}
     */
    public function getClientId(): string|int
    {
        return self::__getProxyInstance()->getClientId(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): string
    {
        return self::__getProxyInstance()->getData(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatData(): mixed
    {
        return self::__getProxyInstance()->getFormatData(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getOpcode(): int
    {
        return self::__getProxyInstance()->getOpcode(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function isFinish(): bool
    {
        return self::__getProxyInstance()->isFinish(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): \Imi\Util\Socket\IPEndPoint
    {
        return self::__getProxyInstance()->getClientAddress(...\func_get_args());
    }
}
