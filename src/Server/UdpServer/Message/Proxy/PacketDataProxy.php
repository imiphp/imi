<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;
use Imi\Util\Socket\IPEndPoint;

/**
 * @Bean(name="UdpPacketDataProxy", recursion=false)
 * @RequestContextProxy(class="Imi\Server\UdpServer\Message\IPacketData", name="packetData")
 *
 * @method string                      getData()
 * @method static                      string getData()
 * @method mixed                       getFormatData()
 * @method static                      mixed getFormatData()
 * @method \Imi\Util\Socket\IPEndPoint getFormatData()
 * @method static                      \Imi\Util\Socket\IPEndPoint getFormatData()
 */
class PacketDataProxy extends BaseRequestContextProxy implements \Imi\Server\UdpServer\Message\IPacketData
{
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
    public function getClientAddress(): IPEndPoint
    {
        return self::__getProxyInstance()->getClientAddress(...\func_get_args());
    }
}
