<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Imi\Util\Http\Consts\StatusCode;
use Yurun\Util\HttpRequest;

use function Imi\env;

/**
 * @testdox Https
 */
class HttpsTest extends BaseTestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->host = env('HTTP_HTTPS_TEST_SERVER_HOST', 'https://127.0.0.1:13006/');
    }

    /**
     * 测试 Uri 地址
     */
    public function testUri(): void
    {
        $http = new HttpRequest();
        $uri = $this->host . 'info?get=1';
        $response = $http->get($uri);
        $data = $response->json(true);
        $this->assertEquals($uri, $data['uri'] ?? null);
        $this->assertEquals($uri, $data['appUri'] ?? null);
    }

    /**
     * 控制器指定 server 测试.
     */
    public function testOutsideController(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'testOutside');
        $this->assertEquals(StatusCode::NOT_FOUND, $response->getStatusCode());
    }
}
