<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Message;

interface IPacketData
{
    /**
     * 数据内容.
     *
     * @return string
     */
    public function getData(): string;

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData();

    /**
     * 获取客户端 IP.
     *
     * @return string
     */
    public function getRemoteIp(): string;

    /**
     * 获取客户端端口.
     *
     * @return int
     */
    public function getRemotePort(): int;

    /**
     * 获取客户端地址
     *
     * @return string
     */
    public function getRemoteAddress(): string;
}
