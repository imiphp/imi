<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Tcp;

abstract class BaseTest extends \Imi\Workerman\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'tcp://127.0.0.1:13003/';
}
