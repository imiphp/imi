<?php
namespace Imi\Test\HttpServer\Tests;

use Yurun\Util\HttpRequest;

/**
 * @testdox Process
 */
class ProcessTest extends BaseTest
{
    /**
     * 测试 @PoolClean 注解，mode=allow
     *
     * @return void
     */
    public function testPoolCleanAllow()
    {
        $file = dirname(__DIR__) . '/bin/imi';
        $cmd = cmd('"' . $file . '" process/start -name PoolTest1');
        $result = `{$cmd}`;
        $list = explode(PHP_EOL, $result);
        end($list);
        prev($list);
        $this->assertEquals(json_encode([
            'maindb'    =>  0,
            'redis'     =>  0,
        ]), prev($list));
    }

    /**
     * 测试 @PoolClean 注解，mode=deny
     *
     * @return void
     */
    public function testPoolCleanDeny()
    {
        $file = dirname(__DIR__) . '/bin/imi';
        $cmd = cmd('"' . $file . '" process/run -name PoolTest2');
        $result = `{$cmd}`;
        $list = explode(PHP_EOL, $result);
        end($list);
        $this->assertEquals(json_encode([
            'maindb'    =>  0,
            'redis'     =>  1,
        ]), prev($list));
    }

    /**
     * getProcessWithManager
     *
     * @return void
     */
    public function testGetProcessWithManager()
    {
        $http = new HttpRequest;
        $response = $http->get($this->host . 'process');
        $data = $response->json(true);
        $this->assertTrue($data['result'] ?? null);
    }

}