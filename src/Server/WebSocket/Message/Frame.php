<?php

namespace Imi\Server\WebSocket\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class Frame implements IFrame
{
    /**
     * swoole websocket frame.
     *
     * @var \Swoole\Websocket\Frame
     */
    protected $frame;

    /**
     * 格式化后的数据.
     *
     * @var array
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
     * @return int
     */
    public function getFd(): int
    {
        return $this->frame->fd;
    }

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     *
     * @return string
     */
    public function getData()
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
     *
     * @return int
     */
    public function getOpcode()
    {
        return $this->frame->opcode;
    }

    /**
     * 表示数据帧是否完整.
     *
     * @return bool
     */
    public function isFinish()
    {
        return $this->frame->finish;
    }

    /**
     * 获取 \Swoole\Websocket\Frame 对象
     *
     * @return \Swoole\Websocket\Frame
     */
    public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame
    {
        return $this->frame;
    }
}
