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
    protected IPEndPoint $clientAddress;

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
     * 数据内容.
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData()
    {
        return $this->formatData;
    }

    /**
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint
    {
        return $this->clientAddress;
    }
}
