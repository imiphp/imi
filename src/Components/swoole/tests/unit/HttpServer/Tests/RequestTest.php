<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\StatusCode;
use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http\Psr7\UploadedFile;

/**
 * @testdox HttpRequest
 */
class RequestTest extends BaseTest
{
    /**
     * route.
     */
    public function testRoute(): void
    {
        $http = new HttpRequest();
        $id = '19260817';
        $response = $http->get($this->host . 'route/' . $id);
        $data = $response->json(true);
        $this->assertEquals($id, $data['id'] ?? null);
    }

    /**
     * route autoEndSlash.
     */
    public function testAutoEndSlash(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'html/?time=' . time());
        $this->assertEquals('<p>' . date('Y-m-d H:i:s', $time) . '</p>', $response->body());
    }

    /**
     * $_GET.
     */
    public function testGetParams(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'info?time=' . $time);
        $data = $response->json(true);
        $this->assertEquals($time, $data['get']['time'] ?? null);
    }

    /**
     * $_POST.
     */
    public function testPostParams(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->post($this->host . 'info', [
            'time'  => $time,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time, $data['post']['time'] ?? null);
    }

    /**
     * $_COOKIE.
     */
    public function testCookieParams(): void
    {
        $http = new HttpRequest();
        $time = time();
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)
                            ->cookies([
                                'time'  => $time,
                            ])
                            ->get($this->host . 'info');
        $data = $response->json(true);
        $this->assertEquals($time, $data['cookie']['time'] ?? null);
        $this->assertEquals($hash, $data['cookie']['hash'] ?? null);
    }

    /**
     * $_REQUEST.
     */
    public function testRequestParams(): void
    {
        $http = new HttpRequest();
        $time1 = (string) microtime(true);
        $time2 = (string) microtime(true);
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)->post($this->host . 'info?time1=' . $time1, [
            'time2'  => $time2,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time1, $data['request']['time1'] ?? null, 'Request\'s get params fail');
        $this->assertEquals($time2, $data['request']['time2'] ?? null, 'Request\'s post params fail');
        $this->assertEquals($hash, $data['request']['hash'] ?? null, 'Request\'s cookie params fail');
    }

    /**
     * Request Header.
     */
    public function testRequestHeaders(): void
    {
        $http = new HttpRequest();
        $time = (string) time();
        $hash = uniqid();
        $response = $http->header('hash', $hash)
                            ->headers([
                                'time'  => $time,
                            ])
                            ->get($this->host . 'info');
        $data = $response->json(true);
        $this->assertEquals($time, $data['headers']['time'] ?? null);
        $this->assertEquals($hash, $data['headers']['hash'] ?? null);
    }

    /**
     * Upload single file.
     */
    public function testUploadSingle(): void
    {
        $http = new HttpRequest();
        $file = new UploadedFile(basename(__FILE__), MediaType::TEXT_HTML, __FILE__);
        $http->content([
            'file'  => $file,
        ]);
        $response = $http->post($this->host . 'upload');
        $data = $response->json(true);

        $this->assertTrue(isset($data['file']));
        $file = $data['file'];
        $content = file_get_contents(__FILE__);
        $this->assertEquals(basename(__FILE__), $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_HTML, $file['clientMediaType']);
        $this->assertEquals(\strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);
    }

    /**
     * Upload multi files.
     */
    public function testUploadMulti(): void
    {
        $http = new HttpRequest();
        $file2Path = __DIR__ . '/1.txt';
        $file1 = new UploadedFile(basename(__FILE__), MediaType::TEXT_HTML, __FILE__);
        $file2 = new UploadedFile(basename($file2Path), MediaType::TEXT_PLAIN, $file2Path);
        $http->content([
            'file1' => $file1,
            'file2' => $file2,
        ]);
        $response = $http->post($this->host . 'upload');
        $data = $response->json(true);

        $this->assertTrue(isset($data['file1']));
        $file = $data['file1'];
        $content = file_get_contents(__FILE__);
        $this->assertEquals(basename(__FILE__), $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_HTML, $file['clientMediaType']);
        $this->assertEquals(\strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);

        $this->assertTrue(isset($data['file2']));
        $file = $data['file2'];
        $content = file_get_contents($file2Path);
        $this->assertEquals(basename($file2Path), $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_PLAIN, $file['clientMediaType']);
        $this->assertEquals(\strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);
    }

    public function testUpload2(): void
    {
        $http = new HttpRequest();
        $response = $http->post($this->host . 'upload2');
        $data = $response->json(true);
        $this->assertEquals('Missing uploaded file: file', $data['message'] ?? null);

        $file = new UploadedFile(basename(__FILE__), MediaType::TEXT_HTML, __FILE__);
        $http->content([
            'file'  => $file,
        ]);
        $response = $http->post($this->host . 'upload2');
        $data = $response->json(true);

        $this->assertTrue(isset($data['data']));
        $file = $data['data'];
        $content = file_get_contents(__FILE__);
        $this->assertEquals(basename(__FILE__), $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_HTML, $file['clientMediaType']);
        $this->assertEquals(\strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);
    }

    /**
     * 控制器不在服务器目录下的测试.
     */
    public function testOutsideController(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'testOutside');
        $data = $response->json(true);
        $this->assertEquals('testOutside', $data['action'] ?? null);
    }

    /**
     * 测试动作传入的参数值
     */
    public function testActionProperty(): void
    {
        $http = new HttpRequest();
        $response = $http->post($this->host . 'info2?get=1', 'post=2');
        $data = $response->json(true);
        $this->assertEquals([
            'get'   => ['get' => 1],
            'post'  => ['post' => 2],
        ], $data);

        $response = $http->header('Content-Type', 'application/json')->post($this->host . 'info3?get=1', json_encode([
            'parsedBody'    => 3,
        ]));
        $data = $response->json(true);
        $this->assertEquals([
            'get'           => ['get' => 1],
            'post'          => [
                'parsedBody'    => 3,
            ],
            'parsedBody'    => [
                'parsedBody'    => 3,
            ],
            'default'       => 19260817,
        ], $data);
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
        $this->assertEquals(TEST_APP_URI, $data['appUri'] ?? null);
    }

    /**
     * 测试执行超时.
     */
    public function testExecuteTimeout(): void
    {
        $http = new HttpRequest();
        $time = microtime(true);
        $response = $http->get($this->host . 'executeTimeout');
        $time = microtime(true) - $time;
        $this->assertLessThan(2, $time);
        $this->assertEquals('<h1>Request execute timeout</h1>', $response->body());
    }

    /**
     * 测试未找到匹配路由情况.
     */
    public function testRouteNotFound(): void
    {
        $http = new HttpRequest();
        $uri = $this->host . 'testRouteNotFound';
        $response = $http->get($uri);
        $this->assertEquals('gg', $response->body());
    }

    /**
     * 测试正则路由.
     */
    public function testregularExpressionRoute(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'a/123/1');
        $this->assertEquals(json_encode([
            'id'    => 123,
            'page'  => 1,
        ]), $response->body());

        $response = $http->get($this->host . 'a/1234/1');
        $this->assertEquals('gg', $response->body());

        $response = $http->get($this->host . 'a/abc/2');
        $this->assertEquals(json_encode([
            'name'  => 'abc',
            'page'  => 2,
        ]), $response->body());
    }

    public function testMoreUrlParams(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'type/1/test/666');
        $this->assertEquals(json_encode([
            'id'    => 1,
            'name'  => 'test',
            'page'  => 666,
        ]), $response->body());
    }

    /**
     * Annotation ExtractData.
     */
    public function testExtractData(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'extractData?id=123');
        $this->assertEquals(json_encode([
            'id'    => 123,
            'id2'   => 123,
            'id3'   => '123',
        ]), $response->body());
    }

    /**
     * Annotation RequestParam.
     */
    public function testRequestParam(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'requestParam1?id=123');
        $this->assertEquals(json_encode([
            'id'    => 123,
            'id2'   => 123,
            'id3'   => 'imi 666',
        ]), $response->body());

        $response = $http->get($this->host . 'requestParam1?id=123&id3=456');
        $this->assertEquals(json_encode([
            'id'    => 123,
            'id2'   => 123,
            'id3'   => '456',
        ]), $response->body());

        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            $response = $http->get($this->host . 'requestParam2?id=123');
            $this->assertEquals(json_encode([
                'id'    => 123,
                'id2'   => 123,
                'id3'   => 'imi niubi',
            ]), $response->body());

            $response = $http->get($this->host . 'requestParam2?id=123&id3=456');
            $this->assertEquals(json_encode([
                'id'    => 123,
                'id2'   => 123,
                'id3'   => '456',
            ]), $response->body());
        }
    }

    public function testIgnoreCase(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'ignoreCase');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $response = $http->get($this->host . 'IgnoreCase');
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
    }

    public function testDomain(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'domain');
        $this->assertEquals('gg', $response->body());

        $response = $http->header('host', 'localhost')->get($this->host . 'domain');
        $this->assertNotEquals('gg', $response->body());

        $http = new HttpRequest();
        $response = $http->get($this->host . 'domain2');
        $this->assertEquals('gg', $response->body());

        $response = $http->header('host', 'localhost')->get($this->host . 'domain2');
        $this->assertEquals([
            'value' => 'host',
        ], $response->json(true));
    }

    public function testEnum(): void
    {
        if (\PHP_VERSION_ID < 80100)
        {
            $this->markTestSkipped();
        }
        $http = new HttpRequest();
        $response = $http->get($this->host . 'enum/test1?enum=A&enumBacked=imi');
        $this->assertEquals([
            'enum'       => 'A',
            'enumBacked' => 'imi',
        ], $response->json(true));
        $response = $http->get($this->host . 'enum/test2');
        $this->assertEquals([
            'enum'       => '',
            'enumBacked' => '',
        ], $response->json(true));
        $response = $http->get($this->host . 'enum/test2?enum=A&enumBacked=imi');
        $this->assertEquals([
            'enum'       => 'A',
            'enumBacked' => 'imi',
        ], $response->json(true));
        $response = $http->get($this->host . 'enum/test2?enum=x&enumBacked=x');
        $this->assertEquals([
            'enum'       => 'x',
            'enumBacked' => 'x',
        ], $response->json(true));
    }
}
