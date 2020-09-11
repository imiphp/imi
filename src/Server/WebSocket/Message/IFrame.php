<?php

namespace Imi\Server\WebSocket\Message;

interface IFrame
{
    /**
     * 获取客户端的socket id.
     *
     * @return int
     */
    public function getFd(): int;

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     *
     * @return string
     */
    public function getData();

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData();

    /**
     * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
     * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据.
     *
     * @return int
     */
    public function getOpcode();

    /**
     * 表示数据帧是否完整.
     *
     * @return bool
     */
    public function isFinish();

    /**
     * 获取 \Swoole\Websocket\Frame 对象
     *
     * @return \Swoole\Websocket\Frame
     */
    public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame;
}
