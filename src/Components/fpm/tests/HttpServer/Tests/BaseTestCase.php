<?php

declare(strict_types=1);

namespace Imi\Fpm\Test\HttpServer\Tests;

use function Imi\env;

abstract class BaseTestCase extends \Imi\Fpm\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->host = env('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/');
    }
}
