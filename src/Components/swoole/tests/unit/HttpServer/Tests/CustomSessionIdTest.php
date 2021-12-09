<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use function Imi\env;
use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox HttpSession custom sessionid
 */
class CustomSessionIdTest extends BaseTest
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->host = env('HTTP_SESSION_TEST_SERVER_HOST', 'http://127.0.0.1:13005/');
    }

    public function testSetGetDelete(): void
    {
        $this->go(function () {
            $http = new HttpRequest();
            $response = $http->get($this->host . 'session/login');
            $sessionId = $response->getCookie('imisid');
            Assert::assertNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());
            $sessionId = $response->json(true)['sessionId'] ?? null;
            Assert::assertNotNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());

            $http = new HttpRequest();
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
        });
    }

    public function testOnce(): void
    {
        $this->go(function () {
            $http = new HttpRequest();

            $response = $http->get($this->host . 'session/verifySms?vcode=1234');
            $data = $response->json(true);
            Assert::assertTrue(isset($data['success']) && !$data['success'], 'fail:' . $response->errno() . ':' . $response->error());
            $sessionId = $response->getCookie('imisid');
            Assert::assertNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());

            $http = new HttpRequest();

            $response = $http->get($this->host . 'session/sendSms');
            $sessionId = $response->getCookie('imisid');
            Assert::assertNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());
            $sessionId = $response->json(true)['sessionId'] ?? null;
            Assert::assertNotNull($sessionId, 'fail:' . $response->errno() . ':' . $response->error());
            $http->header('X-Session-ID', $sessionId);

            $response = $http->get($this->host . 'session/verifySms?vcode=1234');
            $data = $response->json(true);
            Assert::assertTrue(isset($data['success']) && $data['success'], 'fail:' . $response->errno() . ':' . $response->error());

            $response = $http->get($this->host . 'session/verifySms?vcode=1234');
            $data = $response->json(true);
            Assert::assertTrue(isset($data['success']) && !$data['success'], 'fail:' . $response->errno() . ':' . $response->error());
        });
    }
}
