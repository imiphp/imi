<?php

declare(strict_types=1);

namespace Imi\Server\MQTT\Message;

use BinSoul\Net\Mqtt\Packet;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class ReceiveData implements IReceiveData
{
    /**
     * 接收到的数据.
     */
    protected ?Packet $formatData = null;

    /**
     * 客户端地址
     */
    protected ?IPEndPoint $clientAddress = null;

    public function __construct(
        /**
         * 客户端连接的标识符.
         */
        protected int|string $clientId,
        /**
         * Reactor线程ID.
         */
        protected int $reactorId, protected string $data)
    {
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * {@inheritDoc}
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
