<?php

declare(strict_types=1);

namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox SingletonRequestTest
 */
class SingletonRequestTest extends BaseTest
{
    /**
     * $_GET.
     *
     * @return void
     */
    public function testGetParams()
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'singletonRequest?time=' . $time);
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['get']['time']) ? $data['get']['time'] : null);
    }

    /**
     * $_POST.
     *
     * @return void
     */
    public function testPostParams()
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->post($this->host . 'singletonRequest', [
            'time'  => $time,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['post']['time']) ? $data['post']['time'] : null);
    }

    /**
     * $_COOKIE.
     *
     * @return void
     */
    public function testCookieParams()
    {
        $http = new HttpRequest();
        $time = time();
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)
                            ->cookies([
                                'time'  => $time,
                            ])
                            ->get($this->host . 'singletonRequest');
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['cookie']['time']) ? $data['cookie']['time'] : null);
        $this->assertEquals($hash, isset($data['cookie']['hash']) ? $data['cookie']['hash'] : null);
    }

    /**
     * $_REQUEST.
     *
     * @return void
     */
    public function testRequestParams()
    {
        $http = new HttpRequest();
        $time1 = (string) microtime(true);
        $time2 = (string) microtime(true);
        $hash = uniqid();
        $response = $http->cookie('hash', $hash)->post($this->host . 'singletonRequest?time1=' . $time1, [
            'time2'  => $time2,
        ]);
        $data = $response->json(true);
        $this->assertEquals($time1, $data['request']['time1'] ?? null, 'Request\'s get params fail');
        $this->assertEquals($time2, $data['request']['time2'] ?? null, 'Request\'s post params fail');
        $this->assertEquals($hash, $data['request']['hash'] ?? null, 'Request\'s cookie params fail');
    }

    /**
     * Request Header.
     *
     * @return void
     */
    public function testRequestHeaders()
    {
        $http = new HttpRequest();
        $time = (string) time();
        $hash = uniqid();
        $response = $http->header('hash', $hash)
                            ->headers([
                                'time'  => $time,
                            ])
                            ->get($this->host . 'singletonRequest');
        $data = $response->json(true);
        $this->assertEquals($time, isset($data['headers']['time']) ? $data['headers']['time'] : null);
        $this->assertEquals($hash, isset($data['headers']['hash']) ? $data['headers']['hash'] : null);
    }
}
