<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @Bean(name="UdpPacketDataProxy")
 * @RequestContextProxy(class="Imi\Server\UdpServer\Message\IPacketData", name="packetData")
 *
 * @method string getData()
 * @method static string getData()
 * @method mixed  getFormatData()
 * @method static mixed getFormatData()
 * @method string getRemoteIp()
 * @method static string getRemoteIp()
 * @method int    getRemotePort()
 * @method static int getRemotePort()
 * @method string getRemoteAddress()
 * @method static string getRemoteAddress()
 */
class PacketDataProxy extends BaseRequestContextProxy implements \Imi\Server\UdpServer\Message\IPacketData
{
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
    public function getRemoteIp(): string
    {
        return $this->__getProxyInstance()->getRemoteIp(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getRemotePort(): int
    {
        return $this->__getProxyInstance()->getRemotePort(...\func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function getRemoteAddress(): string
    {
        return $this->__getProxyInstance()->getRemoteAddress(...\func_get_args());
    }
}
