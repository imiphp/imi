<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

use function Imi\env;

/**
 * @testdox Http2
 */
class Http2Test extends BaseTestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->host = env('HTTP_HTTP2_TEST_SERVER_HOST', 'https://127.0.0.1:13007/');
    }

    /**
     * 测试 Uri 地址
     */
    public function testUri(): void
    {
        $this->go(function (): void {
            $http = new HttpRequest();
            $http->protocolVersion = '2.0';
            $uri = $this->host . 'info?get=1';
            $response = $http->get($uri);
            $data = $response->json(true);
            $this->assertEquals($uri, $data['uri'] ?? null);
            $this->assertEquals('HTTP/2', $data['server']['server_protocol'] ?? null);
            $this->assertEquals('yurun', $response->getHeaderLine('trailer'));
            $this->assertEquals('niubi', $response->getHeaderLine('yurun'));
            $this->assertEquals(1, $data['ConnectionContext']['count'] ?? null);

            $response = $http->get($uri);
            $data = $response->json(true);
            $this->assertEquals(2, $data['ConnectionContext']['count'] ?? null);
        });
    }
}
