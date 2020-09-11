<?php

namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp;

/**
 * @testdox Http2
 */
class Http2Test extends BaseTest
{
    /**
     * @param string $name
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->host = imiGetEnv('HTTP_HTTP2_TEST_SERVER_HOST', 'https://127.0.0.1:13007/');
    }

    /**
     * 测试 Uri 地址
     *
     * @return void
     */
    public function testUri()
    {
        $this->go(function () {
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
            $http = new HttpRequest();
            $http->protocolVersion = '2.0';
            $uri = $this->host . 'info?get=1';
            $response = $http->get($uri);
            $data = $response->json(true);
            $this->assertEquals($uri, $data['uri'] ?? null);
            $this->assertEquals('HTTP/2', $data['server']['server_protocol'] ?? null);
            $this->assertEquals('yurun', $response->getHeaderLine('trailer'));
            $this->assertEquals('niubi', $response->getHeaderLine('yurun'));
            $this->assertEquals(1, $data['connectContext']['count'] ?? null);

            $response = $http->get($uri);
            $data = $response->json(true);
            $this->assertEquals(2, $data['connectContext']['count'] ?? null);
        }, function () {
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Curl::class);
        });
    }
}
