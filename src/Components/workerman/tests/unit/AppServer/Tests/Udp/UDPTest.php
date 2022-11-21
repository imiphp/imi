<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Udp;

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
            $fp = stream_socket_client($this->host);
            $this->assertIsResource($fp);
            stream_set_timeout($fp, 3);
            $time = time();
            $format = 'Y-m-d H:i:s';
            $this->assertNotFalse(fwrite($fp, json_encode([
                'action'    => 'hello',
                'format'    => $format,
                'time'      => $time,
            ])));
            $recv = fgets($fp);
            $this->assertNotFalse($recv);
            $data = json_decode($recv, true);
            $this->assertEquals(date($format, $time), $data['time'] ?? null);
        });
    }

    public function testNotFound(): void
    {
        $this->go(function () {
            $fp = stream_socket_client($this->host);
            $this->assertIsResource($fp);
            stream_set_timeout($fp, 3);
            $this->assertNotFalse(fwrite($fp, json_encode([
                'action'    => 'gg',
            ])));
            $data = fgets($fp);
            $this->assertNotFalse($data);
            $this->assertEquals('"gg"', $data);
        });
    }
}
