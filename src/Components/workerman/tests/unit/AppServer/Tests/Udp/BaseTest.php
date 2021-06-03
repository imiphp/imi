<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Udp;

abstract class BaseTest extends \Imi\Workerman\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'udp://127.0.0.1:13004/';
}
