<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\Tests;

use Imi\Util\Uri;

/**
 * @testdox UDP
 */
class UDPTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test(): void
    {
        $this->go(function () {
            $uri = new Uri($this->host);
            $client = new \Swoole\Coroutine\Client(\SWOOLE_SOCK_UDP);
            $time = time();
            $format = 'Y-m-d H:i:s';
            $this->assertTrue($client->sendto($uri->getHost(), $uri->getPort(), json_encode([
                'action'    => 'hello',
                'format'    => $format,
                'time'      => $time,
            ])));
            $data = json_decode($client->recv(), true);
            $this->assertEquals(date($format, $time), $data['time'] ?? null);
        });
    }

    public function testNotFound(): void
    {
        $this->go(function () {
            $uri = new Uri($this->host);
            $client = new \Swoole\Coroutine\Client(\SWOOLE_SOCK_UDP);
            $time = time();
            $format = 'Y-m-d H:i:s';
            $this->assertTrue($client->sendto($uri->getHost(), $uri->getPort(), json_encode([
                'action'    => 'gg',
            ])));
            $data = $client->recv();
            $this->assertEquals('"gg"', $data);
        });
    }
}
