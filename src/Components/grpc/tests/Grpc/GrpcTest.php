<?php

namespace Imi\Grpc\Test;

use Yurun\Util\HttpRequest;

class GrpcTest extends BaseTest
{
    public function testGrpc(): void
    {
        $http = new HttpRequest();
        $response = $http->post('http://127.0.0.1:8081/testLogin', [
            'phone'     => '1',
            'password'  => '2',
        ]);
        $this->assertEquals([
            'success'   => false,
            'error'     => '登录失败',
        ], $response->json(true));

        $response = $http->post('http://127.0.0.1:8081/testLogin', [
            'phone'     => '12345678901',
            'password'  => '123456',
        ]);
        $this->assertEquals([
            'success'   => true,
            'error'     => '',
        ], $response->json(true));
    }
}
