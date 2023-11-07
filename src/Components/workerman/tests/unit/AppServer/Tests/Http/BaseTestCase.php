<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Http;

use function Imi\env;

abstract class BaseTestCase extends \Imi\Workerman\Test\BaseTestCase
{
    /**
     * 请求主机.
     */
    protected string $host;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->host = env('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/');
    }
}
