<?php

declare(strict_types=1);

namespace Imi\Fpm\Test\Web\Tests;

abstract class BaseTest extends \Imi\Fpm\Test\BaseTest
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
        $this->host = imiGetEnv('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/');
    }
}
