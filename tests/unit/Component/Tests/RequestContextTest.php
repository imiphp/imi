<?php

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

        $this->assertEquals(0, $count);
        RequestContext::remember($key, $countFun);
        $this->assertEquals(1, $count);
        RequestContext::remember($key, $countFun);
        $this->assertEquals(1, $count);
        RequestContext::getContext()->offsetUnset($key);
        RequestContext::remember($key, $countFun);
        $this->assertEquals(2, $count);
    }
}
