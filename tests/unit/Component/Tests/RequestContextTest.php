<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\RequestContext;
use Imi\Test\BaseTest;

/**
 * @testdox RequestContext
 */
class RequestContextTest extends BaseTest
{
    public function testRemember(): void
    {
        $key = 'test_remember';
        $count = 0;
        $countFun = function () use (&$count) {
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
