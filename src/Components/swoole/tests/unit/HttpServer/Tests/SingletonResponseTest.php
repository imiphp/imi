<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox SingletonResponseTest
 */
class SingletonResponseTest extends BaseTest
{
    /**
     * Response1.
     */
    public function testResponse1(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'singletonResponse1');
        $this->assertEquals('imi niubi-1', $response->body());
    }

    /**
     * Response2.
     */
    public function testResponse2(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'singletonResponse2');
        $this->assertEquals('imi niubi-2', $response->body());
    }
}
