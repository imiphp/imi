<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class PacketData implements IPacketData
{
    /**
     * 客户端 IP.
     */
    protected string $remoteIp = '';

    /**
     * 客户端端口.
     */
    protected int $remotePort = 0;

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
        $this->remoteIp = $remoteIp;
        $this->remotePort = $remotePort;
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
     * 获取客户端 IP.
     */
    public function getRemoteIp(): string
    {
        return $this->remoteIp;
    }

    /**
     * 获取客户端端口.
     */
    public function getRemotePort(): int
    {
        return $this->remotePort;
    }

    /**
     * 获取客户端地址
     */
    public function getRemoteAddress(): string
    {
        return $this->remoteIp . ':' . $this->remotePort;
    }
}
