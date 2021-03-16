<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class Frame implements IFrame
{
    /**
     * 客户端的socket id.
     */
    protected int $fd;

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    protected string $data;

    /**
     * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
     * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据.
     */
    protected int $opcode;

    /**
     * 表示数据帧是否完整.
     */
    protected bool $finish;

    /**
     * 格式化后的数据.
     *
     * @var mixed
     */
    protected $formatData;

    public function __construct(string $data, int $fd, int $opcode = 1, bool $finish = true)
    {
        $this->data = $data;
        $this->fd = $fd;
        $this->opcode = $opcode;
        $this->finish = $finish;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * 获取客户端的socket id.
     */
    public function getFd(): int
    {
        return $this->fd;
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
     * @return mixed
     */
    public function getFormatData()
    {
        return $this->formatData;
    }

    /**
     * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
     * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据.
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * 表示数据帧是否完整.
     */
    public function isFinish(): bool
    {
        return $this->finish;
    }
}
