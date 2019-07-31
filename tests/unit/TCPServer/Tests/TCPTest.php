<?php
namespace Imi\Test\TCPServer\Tests;

use Imi\Util\Uri;

/**
 * @testdox TCP
 */
class TCPTest extends BaseTest
{
    public function test()
    {
        $this->go(function(){
            $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
            $uri = new Uri($this->host);
            $this->assertTrue($client->connect($uri->getHost(), $uri->getPort(), 3));
            $name = 'imitest';
            $sendContent = json_encode([
                'action'    => 'login',
                'username'  => $name,
            ]) . "\r\n";
            $this->assertEquals(strlen($sendContent), $client->send($sendContent));
            $this->assertEquals('{"action":"login","success":true}' . "\r\n", $client->recv());

            $time = time();
            $sendContent = json_encode([
                'action'    => 'send',
                'message'   => $time,
            ]) . "\r\n";
            $this->assertEquals(strlen($sendContent), $client->send($sendContent));
            $this->assertEquals('{"action":"send","message":"' . $name . ':' . $time . '"}' . "\r\n", $client->recv());

            $client->close();
        });
    }

}