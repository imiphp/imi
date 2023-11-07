<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\RedisSessionServer\Tests;

abstract class BaseTestCase extends \Imi\Swoole\Test\BaseTestCase
{
    /**
     * 请求主机.
     */
    protected string $host = 'http://127.0.0.1:13001/';
}
