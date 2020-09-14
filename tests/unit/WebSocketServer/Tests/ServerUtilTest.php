<?php
namespace Imi\Test\WebSocketServer\Tests;

use Swoole\Coroutine\Channel;
use Yurun\Util\YurunHttp;
use Yurun\Util\HttpRequest;

/**
 * @testdox Imi\Server\Server
 */
class ServerUtilTest extends BaseTest
{
    public function testGetServer()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $response = $http->get($this->host . 'serverUtil/getServer');
            $this->assertEquals([
                'null'      =>  'main',
                'main'      =>  'main',
                'notFound'  =>  true,
            ], $response->json(true));
        });
    }

    public function testSendMessage()
    {
        $this->go(function(){
            $http = new HttpRequest;
            $response = $http->get($this->host . 'serverUtil/sendMessage');
            $this->assertEquals([
                'sendMessageAll'    =>  2,
                'sendMessage1'      =>  1,
                'sendMessage2'      =>  2,
                'sendMessageRawAll' =>  2,
                'sendMessageRaw1'   =>  1,
                'sendMessageRaw2'   =>  2,
            ], $response->json(true));
        });
    }

    public function testSend()
    {
        $this->go(function(){
            $channel = new Channel(1);
            $func = function($recvCount) use($channel){
                $dataStr = json_encode([
                    'data'  =>  'test',
                ]);
                $http = new HttpRequest;
                $http->retry = 3;
                $http->timeout = 3000;
                $http->connectTimeout = 3000;
                $client = $http->websocket($this->host);
                $this->assertTrue($client->isConnected());
                $this->assertTrue($client->send(json_encode([
                    'action'    =>  'info',
                ])));
                $recv = $client->recv();
                $recvData = json_decode($recv, true);
                if(!isset($recvData['fd']))
                {
                    $this->assertTrue(false, 'Not found fd');
                }
                $channel->push($recvData['fd']);
                for($i = 0; $i < $recvCount; ++$i)
                {
                    $this->assertEquals($dataStr, $client->recv(10));
                }
                $client->close();
            };
            $waitChannel = new Channel(1);
            go(function() use($func, $waitChannel){
                $func(6);
                $waitChannel->push(1);
            });
            go(function() use($func, $waitChannel){
                $func(4);
                $waitChannel->push(1);
            });
            $fds = [];
            for($i = 0; $i < 2; ++$i)
            {
                $result = $channel->pop(10);
                $this->assertNotFalse($result);
                $fds[] = $result;
            }
            $http = new HttpRequest;
            $response = $http->post($this->host . 'serverUtil/send', [
                'fds'   =>  $fds,
            ], 'json');
            $this->assertEquals([
                'send1'         =>  0,
                'send2'         =>  1,
                'send3'         =>  2,
                'sendRaw1'      =>  0,
                'sendRaw2'      =>  1,
                'sendRaw3'      =>  2,
                'sendToAll'     =>  2,
                'sendRawToAll'  =>  2,
            ], $response->json(true));
            for($i = 0; $i < 2; ++$i)
            {
                if(false === $waitChannel->pop())
                {
                    break;
                }
            }
        });
    }

    public function testSendToGroup()
    {
        $this->go(function(){
            $waitChannel = new Channel(1);
            $func = function($recvCount) use($waitChannel){
                $dataStr = json_encode([
                    'data'  =>  'test',
                ]);
                $http = new HttpRequest;
                $http->retry = 3;
                $http->timeout = 3000;
                $http->connectTimeout = 3000;
                $client = $http->websocket($this->host);
                $this->assertTrue($client->isConnected());
                $this->assertTrue($client->send(json_encode([
                    'action'    =>  'login',
                    'username'  =>  uniqid('', true),
                ])));
                $recv = $client->recv();
                $recvData = json_decode($recv, true);
                $this->assertTrue($recvData['success'] ?? null, 'Not found success');
                $waitChannel->push(1);
                for($i = 0; $i < $recvCount; ++$i)
                {
                    $this->assertEquals($dataStr, $client->recv(10));
                }
                $client->close();
            };
            go(function() use($func, $waitChannel){
                $func(2);
                $waitChannel->push(1);
            });
            go(function() use($func, $waitChannel){
                $func(2);
                $waitChannel->push(1);
            });
            for($i = 0; $i < 2; ++$i)
            {
                if(false === $waitChannel->pop())
                {
                    break;
                }
            }
            $http = new HttpRequest;
            $response = $http->get($this->host . 'serverUtil/sendToGroup');
            $this->assertEquals([
                'sendToGroup'       =>  2,
                'sendRawToGroup'    =>  2,
            ], $response->json(true));
            for($i = 0; $i < 2; ++$i)
            {
                if(false === $waitChannel->pop())
                {
                    break;
                }
            }
        });
    }

}
