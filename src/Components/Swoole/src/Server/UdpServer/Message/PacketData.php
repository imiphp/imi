<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class PacketData implements IPacketData
{
    /**
     * 数据内容.
     *
     * @var string
     */
    protected string $data = '';

    /**
     * 接收到的数据.
     *
     * @var mixed
     */
    protected $formatData;

    /**
     * 客户端信息.
     *
     * @var array
     */
    protected array $clientInfo = [];

    public function __construct(string $data, array $clientInfo)
    {
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
        $this->clientInfo = $clientInfo;
    }

    /**
     * 数据内容.
     *
     * @return string
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
     * 获取客户端信息.
     *
     * @return array
     */
    public function getClientInfo(): array
    {
        return $this->clientInfo;
    }
}
