<?php

namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Rest
 */
class RestTest extends BaseTest
{
    /**
     * @return void
     */
    public function testQuery()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'rest');
        $data = $response->json(true);
        $this->assertEquals([
            'list' => [1, 2, 3],
        ], $data);
    }

    /**
     * @return void
     */
    public function testFind()
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'rest/1');
        $data = $response->json(true);
        $this->assertEquals([
            'id' => 1,
        ], $data);
    }

    /**
     * @return void
     */
    public function testCreate()
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

    /**
     * @return void
     */
    public function testUpdate()
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

    /**
     * @return void
     */
    public function testDelete()
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
