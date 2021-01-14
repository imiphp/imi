<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\Tests;

abstract class BaseTest extends \Imi\Swoole\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'tcp://127.0.0.1:13004/';
}
