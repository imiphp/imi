<?php

namespace Imi\Server\WebSocket\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @Bean(name="WebSocketFrameProxy")
 * @RequestContextProxy(class="Imi\Server\WebSocket\Message\IFrame", name="frame")
 *
 * @method int|string getClientId()
 * @method static     int|string getClientId()
 * @method string     getData()
 * @method static     string getData()
 * @method mixed      getFormatData()
 * @method static     mixed getFormatData()
 * @method int        getOpcode()
 * @method static     int getOpcode()
 * @method bool       isFinish()
 * @method static     bool isFinish()
 */
class FrameProxy extends BaseRequestContextProxy implements \Imi\Server\WebSocket\Message\IFrame
{
    /**
     * {@inheritDoc}
     */
    public function getClientId()
    {
        return $this->__getProxyInstance()->getClientId(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): string
    {
        return $this->__getProxyInstance()->getData(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatData()
    {
        return $this->__getProxyInstance()->getFormatData(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getOpcode(): int
    {
        return $this->__getProxyInstance()->getOpcode(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function isFinish(): bool
    {
        return $this->__getProxyInstance()->isFinish(...\func_get_args());
    }
}
