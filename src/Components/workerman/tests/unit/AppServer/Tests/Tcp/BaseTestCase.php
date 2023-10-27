<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Tcp;

abstract class BaseTestCase extends \Imi\Workerman\Test\BaseTestCase
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'tcp://127.0.0.1:13003/';
}
