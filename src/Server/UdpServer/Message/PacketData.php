<?php

namespace Imi\Server\UdpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class PacketData implements IPacketData
{
    /**
     * 数据内容.
     *
     * @var string
     */
    protected $data;

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
    protected $clientInfo;

    public function __construct($data, $clientInfo)
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
    public function getData()
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
    public function getClientInfo()
    {
        return $this->clientInfo;
    }
}
