<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp\Http\Psr7\UploadedFile;
use Imi\Util\Http\Consts\MediaType;

/**
 * @testdox HttpRequest
 */
class RequestTest extends BaseTest
{
    /**
     * $_GET
     *
     * @return void
     */
    public function testGetParams()
    {
        $http = new HttpRequest;
        $time = time();
        $response = $http->get($this->host . 'info?time=' . $time);
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['get']['time']) ? $data['get']['time'] : null);
    }

    /**
     * $_POST
     *
     * @return void
     */
    public function testPostParams()
    {
        $http = new HttpRequest;
        $time = time();
        $response = $http->post($this->host . 'info', [
            'time'  =>  $time,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['post']['time']) ? $data['post']['time'] : null);
    }

    /**
     * $_COOKIE
     *
     * @return void
     */
    public function testCookieParams()
    {
        $http = new HttpRequest;
        $time = time();
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)
                            ->cookies([
                                'time'  =>  $time,
                            ])
                            ->get($this->host . 'info');
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['cookie']['time']) ? $data['cookie']['time'] : null);
        $this->assertEquals($hash, isset($data['cookie']['hash']) ? $data['cookie']['hash'] : null);
    }


    /**
     * $_REQUEST
     *
     * @return void
     */
    public function testRequestParams()
    {
        $http = new HttpRequest;
        $time1 = (string)microtime(true);
        $time2 = (string)microtime(true);
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)->post($this->host . 'info?time1=' . $time1, [
            'time2'  =>  $time2,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time1, $data['request']['time1'] ?? null, 'Request\'s get params fail');
        $this->assertEquals($time2, $data['request']['time2'] ?? null, 'Request\'s post params fail');
        $this->assertEquals($hash, $data['request']['hash'] ?? null, 'Request\'s cookie params fail');
    }

    /**
     * Request Header
     *
     * @return void
     */
    public function testRequestHeaders()
    {
        $http = new HttpRequest;
        $time = (string)time();
        $hash = uniqid();
        $response = $http->header('hash', $hash)
                            ->headers([
                                'time'  =>  $time,
                            ])
                            ->get($this->host . 'info');
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['headers']['time']) ? $data['headers']['time'] : null);
        $this->assertEquals($hash, isset($data['headers']['hash']) ? $data['headers']['hash'] : null);
    }

    /**
     * Upload single file
     *
     * @return void
     */
    public function testUploadSingle()
    {
        $http = new HttpRequest;
        $file = new UploadedFile('file', MediaType::TEXT_HTML, __FILE__);
        $http->content([
            $file,
        ]);
        $response = $http->post($this->host . 'upload');
        $data = $response->json(true);

        $this->assertTrue(isset($data['file']));
        $file = $data['file'];
        $content = file_get_contents(__FILE__);
        $this->assertEquals('file', $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_HTML, $file['clientMediaType']);
        $this->assertEquals(strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);
    }

    /**
     * Upload multi files
     *
     * @return void
     */
    public function testUploadMulti()
    {
        $http = new HttpRequest;
        $file2Path = __DIR__ . '/1.txt';
        $file1 = new UploadedFile('file1', MediaType::TEXT_HTML, __FILE__);
        $file2 = new UploadedFile('file2', MediaType::TEXT_PLAIN, $file2Path);
        $http->content([
            $file1,
            $file2,
        ]);
        $response = $http->post($this->host . 'upload');
        $data = $response->json(true);

        $this->assertTrue(isset($data['file1']));
        $file = $data['file1'];
        $content = file_get_contents(__FILE__);
        $this->assertEquals('file1', $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_HTML, $file['clientMediaType']);
        $this->assertEquals(strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);

        $this->assertTrue(isset($data['file2']));
        $file = $data['file2'];
        $content = file_get_contents($file2Path);
        $this->assertEquals('file2', $file['clientFilename']);
        $this->assertEquals(MediaType::TEXT_PLAIN, $file['clientMediaType']);
        $this->assertEquals(strlen($content), $file['size']);
        $this->assertEquals(md5($content), $file['hash']);
    }

    /**
     * 控制器不在服务器目录下的测试
     *
     * @return void
     */
    public function testOutsideController()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'testOutside');
        $data = $response->json(true);
        $this->assertEquals('testOutside', $data['action'] ?? null);
    }

    /**
     * 测试动作传入的参数值
     *
     * @return void
     */
    public function testActionProperty()
    {
        $http = new HttpRequest;
        $response = $http->post($this->host . 'info2?get=1', 'post=2');
        $data = $response->json(true);
        $this->assertEquals([
            'get'   =>  ['get' => 1],
            'post'  =>  ['post' => 2],
        ], $data);

        $response = $http->header('Content-Type', 'application/json')->post($this->host . 'info3?get=1', json_encode([
            'parsedBody'    =>  3,
        ]));
        $data = $response->json(true);
        $this->assertEquals([
            'get'           =>  ['get' => 1],
            'post'          =>  [],
            'parsedBody'    =>  [
                'parsedBody'    =>  3,
            ],
            'default'       =>  19260817,
        ], $data);
    }

}