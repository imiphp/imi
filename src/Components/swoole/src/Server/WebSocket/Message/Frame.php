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
     * 获取客户端的socket id.
     *
     * @return int|string
     */
    public function getClientId()
    {
        return $this->frame->fd;
    }

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    public function getData(): string
    {
        return $this->frame->data;
    }

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData()
    {
        return $this->data;
    }

    /**
     * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
     * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据.
     */
    public function getOpcode(): int
    {
        return $this->frame->opcode;
    }

    /**
     * 表示数据帧是否完整.
     */
    public function isFinish(): bool
    {
        return $this->frame->finish;
    }

    /**
     * 获取 \Swoole\Websocket\Frame 对象
     */
    public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame
    {
        return $this->frame;
    }

    /**
     * 获取客户端地址
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
