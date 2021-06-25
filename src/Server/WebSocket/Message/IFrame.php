<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Message;

use Imi\Util\Socket\IPEndPoint;

interface IFrame
{
    /**
     * 获取客户端的socket id.
     *
     * @return int|string
     */
    public function getClientId();

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    public function getData(): string;

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
     */
    public function getOpcode(): int;

    /**
     * 表示数据帧是否完整.
     */
    public function isFinish(): bool;

    /**
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint;
}
