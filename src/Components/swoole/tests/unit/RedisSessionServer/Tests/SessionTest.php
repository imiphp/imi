<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\RedisSessionServer\Tests;

use PHPUnit\Framework\Assert;
use Yurun\Util\HttpRequest;

/**
 * @testdox HttpSession RedisHandler
 */
class SessionTest extends BaseTest
{
    public function testSetGetDelete(): void
    {
        $http = new HttpRequest();
        $http->get($this->host . 'session/login');

        $response = $http->get($this->host . 'session/status');
        // lifetime = 0 测试
        $this->assertEquals(0, $http->getHandler()->getCookieManager()->getCookieItem('imisid')->expires ?? null);
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
