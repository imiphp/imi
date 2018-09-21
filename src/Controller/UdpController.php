<?php
namespace Imi\Controller;

/**
 * UDP 控制器
 */
abstract class UdpController
{
    /**
     * 请求
     * @var \Imi\Server\Udp\Server
     */
    public $server;

    /**
     * 桢
     * @var \Imi\Server\UdpServer\Message\IPacketData
     */
    public $data;
}