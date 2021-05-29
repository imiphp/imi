<?php

namespace Imi\Test\HttpServer\Tests;

use Imi\Util\Http\Consts\StatusCode;
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

    /**
     * 控制器指定 server 测试.
     *
     * @return void
     */
    public function testOutsideController()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'testOutside');
        $this->assertEquals(StatusCode::NOT_FOUND, $response->getStatusCode());
    }
}
