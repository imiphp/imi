<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class PacketData implements IPacketData
{
    /**
     * 客户端地址
     */
    protected ?IPEndPoint $clientAddress = null;

    /**
     * 数据内容.
     */
    protected string $data = '';

    /**
     * 接收到的数据.
     *
     * @var mixed
     */
    protected $formatData;

    public function __construct(string $remoteIp, int $remotePort, string $data)
    {
        $this->clientAddress = new IPEndPoint($remoteIp, $remotePort);
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatData()
    {
        return $this->formatData;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): IPEndPoint
    {
        return $this->clientAddress;
    }
}
