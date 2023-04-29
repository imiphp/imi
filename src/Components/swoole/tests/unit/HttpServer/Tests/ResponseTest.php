<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\StatusCode;
use Yurun\Util\HttpRequest;

/**
 * @testdox HttpResponse
 */
class ResponseTest extends BaseTest
{
    /**
     * Middleware.
     */
    public function testMiddleware(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'middleware');
        // 全局中间件
        $this->assertEquals('imiphp.com', $response->getHeaderLine('X-Powered-By'));
        // 局部中间件
        $this->assertEquals('1', $response->getHeaderLine('imi-middleware-1'));
        $this->assertEquals('2', $response->getHeaderLine('imi-middleware-2'));
        $this->assertEquals('3', $response->getHeaderLine('imi-middleware-3'));
        $this->assertEquals('4', $response->getHeaderLine('imi-middleware-4'));
    }

    /**
     * Options Middleware.
     */
    public function testOptionsMiddleware(): void
    {
        $http = new HttpRequest();
        $response = $http->send($this->host, '', 'OPTIONS');
        // OPTIONS 中间件
        $this->assertEquals('http://127.0.0.1', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie', $response->getHeaderLine('Access-Control-Allow-Headers'));
        $this->assertEquals('Authorization, Content-Type, Accept, Origin, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, X-Id, X-Token, Cookie', $response->getHeaderLine('Access-Control-Expose-Headers'));
        $this->assertEquals('GET, POST, PATCH, PUT, DELETE', $response->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('true', $response->getHeaderLine('Access-Control-Allow-Credentials'));
    }

    /**
     * Cookie.
     */
    public function testCookie(): void
    {
        $http = new HttpRequest();
        $http->get($this->host . 'cookie');
        $cookieManager = $http->getHandler()->getCookieManager();

        $this->assertNotNull($a = $cookieManager->getCookieItem('a'));
        $this->assertEquals('1', $a->value);

        $this->assertNotNull($b = $cookieManager->getCookieItem('b'));
        $this->assertEquals('2', $b->value);

        $this->assertNotNull($c = $cookieManager->getCookieItem('c'));
        $this->assertEquals('3', $c->value);

        $this->assertNotNull($d = $cookieManager->getCookieItem('d', '', '/a'));
        $this->assertEquals('4', $d->value);

        $this->assertNotNull($e = $cookieManager->getCookieItem('e', 'localhost', '/'));
        $this->assertEquals('5', $e->value);

        $this->assertNotNull($f = $cookieManager->getCookieItem('f'));
        $this->assertEquals('6', $f->value);
        $this->assertTrue($f->secure);
        $this->assertNotTrue($f->httpOnly);

        $this->assertNotNull($g = $cookieManager->getCookieItem('g'));
        $this->assertEquals('7', $g->value);
        $this->assertTrue($g->secure);
        $this->assertTrue($g->httpOnly);
    }

    /**
     * Headers.
     */
    public function testHeaders(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'headers');

        $this->assertEquals('1,11', $response->getHeaderLine('a'));
        $this->assertEquals('2', $response->getHeaderLine('b'));
    }

    /**
     * Redirect.
     */
    public function testRedirect(): void
    {
        $http = new HttpRequest();
        $http->followLocation = false;
        $response = $http->get($this->host . 'redirect');
        $this->assertEquals(StatusCode::MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaderLine('location'));
    }

    /**
     * Download.
     */
    public function testDownload(): void
    {
        $content = file_get_contents(\dirname(__DIR__) . '/ApiServer/Controller/IndexController.php');

        $http = new HttpRequest();
        $response = $http->get($this->host . 'download');
        $this->assertEquals($content, $response->body());
        $this->assertEquals(MediaType::APPLICATION_OCTET_STREAM, $response->getHeaderLine('content-type'));
        $this->assertEquals('attachment; filename*=UTF-8\'\'IndexController.php', $response->getHeaderLine('content-disposition'));

        $response = $http->get($this->host . 'download', [
            'outputFileName' => '测试.jpg',
        ]);
        $this->assertEquals($content, $response->body());
        $this->assertEquals(MediaType::IMAGE_JPEG, $response->getHeaderLine('content-type'));
        $this->assertEquals('attachment; filename*=UTF-8\'\'' . rawurlencode('测试.jpg'), $response->getHeaderLine('content-disposition'));

        $response = $http->get($this->host . 'download', [
            'contentType'    => MediaType::IMAGE_PNG,
            'outputFileName' => '测试.jpg',
        ]);
        $this->assertEquals($content, $response->body());
        $this->assertEquals(MediaType::IMAGE_PNG, $response->getHeaderLine('content-type'));
        $this->assertEquals('attachment; filename*=UTF-8\'\'' . rawurlencode('测试.jpg'), $response->getHeaderLine('content-disposition'));
    }

    public function testSSE(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'sse');
        $this->assertEquals(MediaType::TEXT_EVENT_STREAM, $response->getHeaderLine('content-type'));
        $result = '';
        foreach (range(1, 100) as $i)
        {
            $result .= 'data: ' . $i . "\n\n";
        }
        $this->assertEquals($result, $response->body());
    }
}
