<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class Frame implements ISwooleWebSocketFrame
{
    /**
     * swoole websocket frame.
     */
    protected \Swoole\WebSocket\Frame $frame;

    /**
     * 客户端地址
     */
    protected IPEndPoint $clientAddress;

    /**
     * 格式化后的数据.
     *
     * @var mixed
     */
    protected $data;

    public function __construct(\Swoole\Websocket\Frame $frame)
    {
        $this->frame = $frame;
        $this->data = RequestContext::getServerBean(DataParser::class)->decode($frame->data);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientId()
    {
        return $this->frame->fd;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): string
    {
        return $this->frame->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getOpcode(): int
    {
        return $this->frame->opcode;
    }

    /**
     * {@inheritDoc}
     */
    public function isFinish(): bool
    {
        return $this->frame->finish;
    }

    /**
     * {@inheritDoc}
     */
    public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame
    {
        return $this->frame;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(): IPEndPoint
    {
        if (!isset($this->clientAddress))
        {
            return $this->clientAddress = RequestContext::getServer()->getClientAddress($this->getClientId());
        }

        return $this->clientAddress;
    }
}
