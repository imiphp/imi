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
     * @param string $name
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->host = imiGetEnv('HTTP_SESSION_TEST_SERVER_HOST', 'http://127.0.0.1:13005/');
    }

    public function testSetGetDelete()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'session/login');
        $sessionId = $response->getCookie('imisid');
        Assert::assertNotNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());

        $http = new HttpRequest;
        $http->header('X-Session-ID', $sessionId);
        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && $data['isLogin'], 'fail:' . $response->errno() . ':' . $response->error());
        Assert::assertEquals('admin', $data['username'], 'fail:' . $response->errno() . ':' . $response->error());

        $http->get($this->host . 'session/logout');

        $response = $http->get($this->host . 'session/status');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['isLogin']) && !$data['isLogin'], 'fail:' . $response->errno() . ':' . $response->error());
        Assert::assertArrayNotHasKey('username', $data, 'fail:' . $response->errno() . ':' . $response->error());

    }

    public function testOnce()
    {
        $http = new HttpRequest;

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success'], 'fail:' . $response->errno() . ':' . $response->error());
        $sessionId = $response->getCookie('imisid');
        Assert::assertNotNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());

        $http = new HttpRequest;
        $http->header('X-Session-ID', $sessionId);

        $response = $http->get($this->host . 'session/sendSms');
        $sessionId = $response->getCookie('imisid');
        Assert::assertNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && $data['success'], 'fail:' . $response->errno() . ':' . $response->error());

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success'], 'fail:' . $response->errno() . ':' . $response->error());
    }

}