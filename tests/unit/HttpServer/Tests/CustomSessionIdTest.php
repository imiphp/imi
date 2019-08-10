<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;
use PHPUnit\Framework\Assert;

/**
 * @testdox HttpSession custom sessionid
 */
class CustomSessionIdTest extends BaseTest
{
    /**
     * 请求主机
     *
     * @var string
     */
    protected $host = 'http://127.0.0.1:13005/';

    public function testSetGetDelete()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'session/login');
        $sessionId = $response->getCookie('imisid');
        Assert::assertNotNull($sessionId);

        $http = new HttpRequest;
        $http->header('X-Session-ID', $sessionId);
        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && $data['isLogin']);
        Assert::assertEquals('admin', $data['username']);

        $http->get($this->host . 'session/logout');

        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && !$data['isLogin']);
        Assert::assertArrayNotHasKey('username', $data);

    }

    public function testOnce()
    {
        $http = new HttpRequest;

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);
        $sessionId = $response->getCookie('imisid');
        Assert::assertNotNull($sessionId);

        $http = new HttpRequest;
        $http->header('X-Session-ID', $sessionId);

        $response = $http->get($this->host . 'session/sendSms');
        $sessionId = $response->getCookie('imisid');
        Assert::assertNull($sessionId);

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && $data['success']);

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);
    }

}