<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox HttpSession FileHandler
 */
class SessionTest extends BaseTestCase
{
    public function testSetGetDelete(): void
    {
        $http = new HttpRequest();
        $http->get($this->host . 'session/login');

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

    public function testOnce(): void
    {
        $http = new HttpRequest();

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);

        $http->get($this->host . 'session/sendSms');

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && $data['success']);

        $response = $http->get($this->host . 'session/verifySms?vcode=1234');
        $data = $response->json(true);
        Assert::assertTrue(isset($data['success']) && !$data['success']);
    }
}
