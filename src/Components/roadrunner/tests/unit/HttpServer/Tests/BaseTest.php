<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Test\HttpServer\Tests;

use function Imi\env;

abstract class BaseTest extends \Imi\RoadRunner\Test\BaseTest
{
    /**
     * 请求主机.
     *
     * @var string
     */
    protected $host;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->host = env('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/');
    }
}
