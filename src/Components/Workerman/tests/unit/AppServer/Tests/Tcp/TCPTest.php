<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\Tests\Tcp;

/**
 * @testdox TCP
 */
class TCPTest extends BaseTest
{
    /**
     * @testdox test
     */
    public function test()
    {
        $this->go(function () {
            $fp = stream_socket_client($this->host);
            $this->assertIsResource($fp);
            $name = 'imitest';
            $sendContent = json_encode([
                'action'    => 'login',
                'username'  => $name,
            ]) . "\n";
            $this->assertEquals(\strlen($sendContent), fwrite($fp, $sendContent));
            $result = fgets($fp);
            $this->assertEquals('{"action":"login","success":true,"middlewareData":"imi"}' . "\n", $result);

            $time = time();
            $sendContent = json_encode([
                'action'    => 'send',
                'message'   => $time,
            ]) . "\n";
            $this->assertEquals(\strlen($sendContent), fwrite($fp, $sendContent));
            $result = fgets($fp);
            $this->assertEquals('{"action":"send","message":"' . $name . ':' . $time . '"}' . "\n", $result);

            fclose($fp);
        });
    }

    public function testNotFound()
    {
        $this->go(function () {
            $fp = stream_socket_client($this->host);
            $this->assertIsResource($fp);
            $sendContent = json_encode([
                'action'    => 'gg',
            ]) . "\n";
            $this->assertEquals(\strlen($sendContent), fwrite($fp, $sendContent));
            $result = fgets($fp);
            $this->assertEquals('"gg"' . "\n", $result);

            fclose($fp);
        });
    }
}
