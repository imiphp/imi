<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\RequestContext;
use Imi\Test\BaseTest;

use function Yurun\Swoole\Coroutine\goWait;

/**
 * @testdox RequestContext
 */
class RequestContextTest extends BaseTest
{
    public function testDefer(): void
    {
        $result = [];
        goWait(static function () use (&$result): void {
            RequestContext::defer(static function () use (&$result): void {
                $result[] = 1;
            });
            RequestContext::defer(static function () use (&$result): void {
                $result[] = 2;
            });
        }, -1, true);
        $this->assertEquals([2, 1], $result);
    }

    public function testRemember(): void
    {
        $key = 'test_remember';
        $count = 0;
        $countFun = static function () use (&$count) {
            return ++$count;
        };

        RequestContext::unset($key);
        $this->assertEquals(0, $count);
        $this->assertEquals(1, RequestContext::remember($key, $countFun));
        $this->assertEquals(1, $count);
        $this->assertEquals(1, RequestContext::remember($key, $countFun));
        $this->assertEquals(1, $count);
        RequestContext::unset($key);
        $this->assertEquals(2, RequestContext::remember($key, $countFun));
        $this->assertEquals(2, $count);
    }
}
