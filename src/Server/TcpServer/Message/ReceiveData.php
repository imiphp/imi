<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class ReceiveData implements IReceiveData
{
    /**
     * 客户端连接的标识符.
     *
     * @var int|string
     */
    protected $clientId = 0;

    /**
     * 接收到的数据.
     */
    protected string $data = '';

    /**
     * 接收到的数据.
     *
     * @var mixed
     */
    protected $formatData;

    /**
     * 客户端地址
     */
    protected IPEndPoint $clientAddress;

    /**
     * @param int|string $clientId
     */
    public function __construct($clientId, string $data)
    {
        $this->clientId = $clientId;
        $this->data = $data;
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
        if (!isset($this->clientAddress))
        {
            return $this->clientAddress = RequestContext::getServer()->getClientAddress($this->clientId);
        }

        return $this->clientAddress;
    }
}
