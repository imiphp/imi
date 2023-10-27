<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

class HttpValidationTest extends BaseTestCase
{
    public function test(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'validate');
        $data = $response->json(true);
        $this->assertEquals('$get.id validate failed', $data['message'] ?? null);

        $response = $http->get($this->host . 'validate?id=1');
        $data = $response->json(true);
        $this->assertEquals(['id' => 1], $data);
    }

    public function testNone(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'validateNone');
        $data = $response->json(true);
        $this->assertEquals([], $data);
    }
}
