<?php

declare(strict_types=1);

namespace Imi\Grpc\Test;

use Grpc\TestRequest;
use Imi\Grpc\Enum\GrpcStatus;
use Imi\Grpc\Proxy\Http\GrpcHttpClient;
use Yurun\Util\HttpRequest;

class GrpcTest extends BaseTestCase
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

    public function testProxy(): void
    {
        $http = new HttpRequest();
        $response = $http->post('http://127.0.0.1:8080/proxy/grpc/grpc.TestService/test', self::DATA, 'json');
        $this->assertEquals(self::DATA, $response->json(true), $response->getHeaderLine('grpc-message'));
    }

    public function testProxyClient(): void
    {
        $client = new GrpcHttpClient('http://127.0.0.1:8080/proxy/grpc');

        $request = new TestRequest();
        $request->setInt(123);

        $responseMessage = $client->request('grpc.TestService', 'test', $request, TestRequest::class, ['grpc-test' => 'abc'], $responseMetadata, $response);

        $this->assertEquals(123, $responseMessage->getInt());
        $metadata = [
            'grpc-test'    => 'abc',
            'grpc-status'  => (string) GrpcStatus::OK,
            'grpc-message' => '',
        ];
        $this->assertEquals($metadata, $responseMetadata);
        $this->assertInstanceOf(\Yurun\Util\YurunHttp\Http\Response::class, $response);
    }
}
