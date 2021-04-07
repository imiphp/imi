<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

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
     * @param int|string $clientId
     */
    public function __construct($clientId, string $data)
    {
        $this->clientId = $clientId;
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * 获取客户端的socket id.
     *
     * @return int|string
     */
    public function getClientId()
    {
        return $this->clientId;
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
}
