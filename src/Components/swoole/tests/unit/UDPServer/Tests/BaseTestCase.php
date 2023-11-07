<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\Tests;

abstract class BaseTestCase extends \Imi\Swoole\Test\BaseTestCase
{
    /**
     * 请求主机.
     */
    protected string $host = 'tcp://127.0.0.1:13004/';
}
