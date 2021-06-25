<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Message;

use Imi\Util\Socket\IPEndPoint;

interface IReceiveData
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
     * 获取客户端地址
     */
    public function getClientAddress(): IPEndPoint;
}
