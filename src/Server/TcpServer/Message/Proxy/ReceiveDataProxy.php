<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Message\Proxy;

use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @method        string|int                  getClientId()
 * @method static string|int                  getClientId()
 * @method        string                      getData()
 * @method static string                      getData()
 * @method        mixed                       getFormatData()
 * @method static mixed                       getFormatData()
 * @method        \Imi\Util\Socket\IPEndPoint getClientAddress()
 * @method static \Imi\Util\Socket\IPEndPoint getClientAddress()
 */
#[
    \Imi\RequestContextProxy\Annotation\RequestContextProxy(class: \Imi\Server\TcpServer\Message\IReceiveData::class, name: 'receiveData'),
    \Imi\Bean\Annotation\Bean(name: 'TcpReceiveDataProxy')
]
class ReceiveDataProxy extends BaseRequestContextProxy implements \Imi\Server\TcpServer\Message\IReceiveData
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
    public function getClientAddress(): \Imi\Util\Socket\IPEndPoint
    {
        return self::__getProxyInstance()->getClientAddress(...\func_get_args());
    }
}
