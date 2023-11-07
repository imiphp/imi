<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\Tests\WebSocket;

abstract class BaseTestCase extends \Imi\WorkermanGateway\Test\BaseTestCase
{
    /**
     * WebSocket 服务请求主机.
     */
    protected string $host = 'ws://127.0.0.1:13002/';

    /**
     * HTTP 服务请求主机.
     */
    protected string $httpHost = 'http://127.0.0.1:13000/';
}
