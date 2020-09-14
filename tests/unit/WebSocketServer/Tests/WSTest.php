<?php
namespace Imi\Test\WebSocketServer\Tests;

use Yurun\Util\HttpRequest;
use Yurun\Util\YurunHttp;

/**
 * @testdox WebSocket
 */
class WSTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $http->retry = 3;
            $http->timeout = 3000;
            $http->connectTimeout = 3000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'login',
                'username'  =>  'test',
            ])));
            $recv = $client->recv();
            $recvData = json_decode($recv, true);
            if(!isset($recvData['token']))
            {
                $this->assertTrue(false, 'Not found token');
            }
            $token = $recvData['token'];
            $fd = $recvData['fd'];
            $this->assertEquals([
                'success'       =>  true,
                'middlewareData'=>  'imi',
                'requestUri'    =>  $this->host,
                'uri'           =>  $this->host,
                'token'         =>  $token,
                'fd'            =>  $fd,
                'getFdByFlag'   =>  $fd,
                'getFlagByFd'   => $token,
                'getFdsByFlags' => [$token => $fd],
                'getFlagsByFds' => [$fd => $token],
            ], $recvData);
            $time = time();
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'send',
                'message'   =>  $time,
            ])));
            $recv = $client->recv();
            $this->assertEquals('test:' . $time, $recv);
            $client->close();

            // 重连逻辑
            $http = new HttpRequest;
            $http->retry = 3;
            $http->timeout = 3000;
            $http->connectTimeout = 3000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());

            // 重试3次
            for($i = 0; $i < 3; ++$i)
            {
                $this->assertTrue($client->send(json_encode([
                    'action'    =>  'reconnect',
                    'token'     =>  $token,
                ])));
                $recv = $client->recv();
                $recvData = json_decode($recv, true);
                if(null !== $recvData)
                {
                    break;
                }
                sleep(1);
            }
            $this->assertEquals([
                'success'   => true,
                'username'  => 'test',
            ], $recvData);
            
            $time = time();
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'send',
                'message'   =>  $time,
            ])));
            $recv = $client->recv();
            $this->assertEquals('test:' . $time, $recv);
            $client->close();

        });
    }

    public function testNotFound()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $http->retry = 3;
            $http->timeout = 3000;
            $http->connectTimeout = 3000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'gg',
            ])));
            $recv = $client->recv();
            $this->assertEquals(json_encode('gg'), $recv);
            $client->close();
        });
    }

    public function testMatchHttpRoute()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $http->retry = 3;
            $http->timeout = 3000;
            $http->connectTimeout = 3000;
            $client = $http->websocket($this->host);
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'test',
                'username'  =>  'test',
            ])));
            $recv = $client->recv();
            $this->assertEquals(json_encode('gg'), $recv);
            $client->close();

            $client = $http->websocket($this->host . 'test');
            $this->assertTrue($client->isConnected());
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'test',
                'username'  =>  'test',
            ])));
            $recv = $client->recv();
            $this->assertEquals(json_encode([
                'data'  =>  [
                    'action'    =>  'test',
                    'username'  =>  'test',
                ],
            ]), $recv);
            $client->close();
        });
    }

    public function testHttp()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $response = $http->get($this->host . 'http');
            $this->assertEquals('http', $response->body());
        });
    }

}