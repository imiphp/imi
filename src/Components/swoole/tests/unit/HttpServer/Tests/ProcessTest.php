<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Process
 */
class ProcessTest extends BaseTest
{
    /**
     * 测试 PoolClean 注解，mode=allow.
     */
    public function testPoolCleanAllow(): void
    {
        $file = \dirname(__DIR__) . '/bin/imi';
        $cmd = \Imi\cmd('"' . $file . '" process/start PoolTest1');
        $result = shell_exec("{$cmd}");
        $list = explode(\PHP_EOL, $result);
        end($list);
        prev($list);
        $this->assertEquals(json_encode([
            'maindb'    => 0,
            'redis'     => 0,
        ]), prev($list));
    }

    /**
     * 测试 PoolClean 注解，mode=deny.
     */
    public function testPoolCleanDeny(): void
    {
        $file = \dirname(__DIR__) . '/bin/imi';
        $cmd = \Imi\cmd('"' . $file . '" process/run PoolTest2');
        $result = shell_exec("{$cmd}");
        $list = explode(\PHP_EOL, $result);
        end($list);
        $this->assertEquals(json_encode([
            'maindb'    => 0,
            'redis'     => 1,
        ]), prev($list));
    }

    /**
     * getProcessWithManager.
     */
    public function testGetProcessWithManager(): void
    {
        $http = new HttpRequest();
        $response = $http->get($this->host . 'process');
        $data = $response->json(true);
        $this->assertTrue($data['result'] ?? null);
    }
}
