<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class ReceiveData implements IReceiveData
{
    /**
     * 客户端连接的标识符.
     *
     * @var string|int
     */
    protected $clientId;

    /**
     * Reactor线程ID.
     *
     * @var int
     */
    protected $reactorId;

    /**
     * 接收到的数据.
     *
     * @var string
     */
    protected $data;

    /**
     * 接收到的数据.
     *
     * @var \BinSoul\Net\Mqtt\Packet
     */
    protected $formatData;

    /**
     * 客户端地址
     */
    protected IPEndPoint $clientAddress;

    /**
     * @param string|int $clientId
     * @param mixed      $data
     */
    public function __construct($clientId, int $reactorId, $data)
    {
        $this->clientId = $clientId;
        $this->reactorId = $reactorId;
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * 获取客户端的socket id.
     *
     * @return int|string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return \BinSoul\Net\Mqtt\Packet
     */
    public function getFormatData()
    {
        return $this->formatData;
    }

    /**
     * 获取Reactor线程ID.
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }

    /**
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint
    {
        if (!isset($this->clientAddress))
        {
            return $this->clientAddress = RequestContext::getServer()->getClientAddress($this->clientId);
        }

        return $this->clientAddress;
    }
}
