<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\Tests;

use function Imi\env;

abstract class BaseTestCase extends \Imi\Swoole\Test\BaseTestCase
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'ws://127.0.0.1:13009/';

    protected function setUp(): void
    {
        if (!env('IMI_TEST_AMQP_SERVER_UTIL', true))
        {
            $this->markTestSkipped('IMI_TEST_AMQP_SERVER_UTIL=0');
        }
    }
}
