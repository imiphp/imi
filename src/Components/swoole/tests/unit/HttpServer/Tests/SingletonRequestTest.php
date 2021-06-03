<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox SingletonRequestTest
 */
class SingletonRequestTest extends BaseTest
{
    /**
     * $_GET.
     */
    public function testGetParams(): void
    {
        $http = new HttpRequest();
        $time = time();
        $response = $http->get($this->host . 'singletonRequest?time=' . $time);
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
        $response = $http->post($this->host . 'singletonRequest', [
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
                            ->get($this->host . 'singletonRequest');
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
                            ->get($this->host . 'singletonRequest');
        $data = $response->json(true);
        $this->assertEquals($time, $data['headers']['time'] ?? null);
        $this->assertEquals($hash, $data['headers']['hash'] ?? null);
    }
}
