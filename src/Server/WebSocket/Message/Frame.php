<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Util\Socket\IPEndPoint;

class Frame implements IFrame
{
    /**
     * 客户端的socket id.
     *
     * @var int|string
     */
    protected $clientId;

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    protected string $data = '';

    /**
     * WebSocket的OpCode类型，可以参考WebSocket协议标准文档
     * WEBSOCKET_OPCODE_TEXT = 0x1 ，文本数据
     * WEBSOCKET_OPCODE_BINARY = 0x2 ，二进制数据.
     */
    protected int $opcode = 0;

    /**
     * 表示数据帧是否完整.
     */
    protected bool $finish = false;

    /**
     * 格式化后的数据.
     *
     * @var mixed
     */
    protected $formatData;

    /**
     * 客户端地址
     */
    protected ?IPEndPoint $clientAddress = null;

    /**
     * @param int|string $clientId
     */
    public function __construct(string $data, $clientId, int $opcode = 1, bool $finish = true)
    {
        $this->data = $data;
        $this->clientId = $clientId;
        $this->opcode = $opcode;
        $this->finish = $finish;
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
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * {@inheritDoc}
     */
    public function isFinish(): bool
    {
        return $this->finish;
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
