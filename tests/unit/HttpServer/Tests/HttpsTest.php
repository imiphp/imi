<?php

declare(strict_types=1);

namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Https
 */
class HttpsTest extends BaseTest
{
    /**
     * @param string $name
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->host = imiGetEnv('HTTP_HTTPS_TEST_SERVER_HOST', 'https://127.0.0.1:13006/');
    }

    /**
     * 测试 Uri 地址
     *
     * @return void
     */
    public function testUri()
    {
        $http = new HttpRequest();
        $uri = $this->host . 'info?get=1';
        $response = $http->get($uri);
        $data = $response->json(true);
        $this->assertEquals($uri, $data['uri'] ?? null);
    }
}
