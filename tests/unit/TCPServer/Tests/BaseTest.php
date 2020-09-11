<?php

namespace Imi\Test\TCPServer\Tests;

abstract class BaseTest extends \Imi\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host = 'tcp://127.0.0.1:13003/';
}
