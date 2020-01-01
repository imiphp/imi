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
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
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
            $this->assertEquals(json_encode([
                'success'       =>  true,
                'middlewareData'=>  'imi',
                'requestUri'    =>  $this->host,
                'uri'           =>  $this->host,
            ]), $recv);
            $time = time();
            $this->assertTrue($client->send(json_encode([
                'action'    =>  'send',
                'message'   =>  $time,
            ])));
            $recv = $client->recv();
            $this->assertEquals('test:' . $time, $recv);
            $client->close();
        }, function(){
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Curl::class);
        });
    }

    public function testNotFound()
    {
        $this->go(function(){
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
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
        }, function(){
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Curl::class);
        });
    }

    public function testMatchHttpRoute()
    {
        $this->go(function(){
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
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
        }, function(){
            YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Curl::class);
        });
    }

}