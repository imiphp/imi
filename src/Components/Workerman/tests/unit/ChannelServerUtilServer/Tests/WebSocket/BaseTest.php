<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\ChannelServerUtilServer\Tests\WebSocket;

abstract class BaseTest extends \Imi\Workerman\Test\BaseTest
{
    /**
     * WebSocket 服务请求主机.
     *
     * @var string
     */
    protected $host = 'ws://127.0.0.1:13007/';

    /**
     * HTTP 服务请求主机.
     *
     * @var string
     */
    protected $httpHost = 'http://127.0.0.1:13006/';
}
