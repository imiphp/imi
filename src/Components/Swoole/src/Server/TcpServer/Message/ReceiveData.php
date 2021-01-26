<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class ReceiveData implements IReceiveData
{
    /**
     * 客户端连接的标识符.
     *
     * @var int
     */
    protected int $fd = 0;

    /**
     * Reactor线程ID.
     *
     * @var int
     */
    protected int $reactorId = 0;

    /**
     * 接收到的数据.
     *
     * @var string
     */
    protected string $data = '';

    /**
     * 接收到的数据.
     *
     * @var mixed
     */
    protected $formatData;

    public function __construct(int $fd, int $reactorId, string $data)
    {
        $this->fd = $fd;
        $this->reactorId = $reactorId;
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * 获取客户端的socket id.
     *
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     *
     * @return string
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
     * 获取Reactor线程ID.
     *
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }
}
