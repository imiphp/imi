<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;
use Imi\Util\Socket\IPEndPoint;

/**
 * @Bean(name="WebSocketFrameProxy")
 * @RequestContextProxy(class="Imi\Server\WebSocket\Message\IFrame", name="frame")
 *
 * @method int|string                  getClientId()
 * @method static                      int|string getClientId()
 * @method string                      getData()
 * @method static                      string getData()
 * @method mixed                       getFormatData()
 * @method static                      mixed getFormatData()
 * @method int                         getOpcode()
 * @method static                      int getOpcode()
 * @method bool                        isFinish()
 * @method static                      bool isFinish()
 * @method \Imi\Util\Socket\IPEndPoint getFormatData()
 * @method static                      \Imi\Util\Socket\IPEndPoint getFormatData()
 */
class FrameProxy extends BaseRequestContextProxy implements \Imi\Server\WebSocket\Message\IFrame
{
    /**
     * {@inheritDoc}
     */
    public function getClientId()
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
    public function getFormatData()
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
    public function getClientAddress(): IPEndPoint
    {
        return self::__getProxyInstance()->getClientAddress(...\func_get_args());
    }
}
