<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

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

}