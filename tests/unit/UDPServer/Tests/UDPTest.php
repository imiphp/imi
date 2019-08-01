<?php
namespace Imi\Test\UDPServer\Tests;

use Imi\Util\Uri;

/**
 * @testdox UDP
 */
class UDPTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test()
    {
        $this->go(function(){
            $uri = new Uri($this->host);
            $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_UDP);
            $time = time();
            $format = 'Y-m-d H:i:s';
            $this->assertTrue($client->sendto($uri->getHost(), $uri->getPort(), json_encode([
                'action'    =>  'hello',
                'format'    =>  $format,
                'time'      =>  $time
            ])));
            $data = json_decode($client->recv(), true);
            $this->assertEquals(date($format, $time), $data['time'] ?? null);
        });
    }

}