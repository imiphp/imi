<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Rest
 */
class RestTest extends BaseTestCase
{
    public function testQuery(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'rest');
        $data = $response->json(true);
        $this->assertEquals([
            'list' => [1, 2, 3],
        ], $data);
    }

    public function testFind(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'rest/1');
        $data = $response->json(true);
        $this->assertEquals([
            'id' => 1,
        ], $data);
    }

    public function testCreate(): void
    {
        $http = new HttpRequest();
        $response = $http->post($this->host . 'rest', [
            'name' => 'imi',
        ]);
        $data = $response->json(true);
        $this->assertEquals([
            'operation' => 'create',
            'name'      => 'imi',
            'success'   => true,
        ], $data);
    }

    public function testUpdate(): void
    {
        $http = new HttpRequest();
        $response = $http->put($this->host . 'rest/123', [
            'name' => 'imi',
        ]);
        $data = $response->json(true);
        $this->assertEquals([
            'id'        => '123',
            'name'      => 'imi',
            'operation' => 'update',
            'success'   => true,
        ], $data);
    }

    public function testDelete(): void
    {
        $http = new HttpRequest();
        $response = $http->delete($this->host . 'rest/123', [
            'name' => 'imi',
        ]);
        $data = $response->json(true);
        $this->assertEquals([
            'id'        => '123',
            'operation' => 'delete',
            'success'   => true,
        ], $data);
    }
}
