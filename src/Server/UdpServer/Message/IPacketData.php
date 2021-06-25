<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message;

use Imi\Util\Socket\IPEndPoint;

interface IPacketData
{
    /**
     * 数据内容.
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
