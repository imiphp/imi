<?php

namespace Imi\Test\Component\Tests;

use Imi\RequestContext;
use Imi\Test\BaseTest;
use Imi\Test\Component\RequestContextProxy\A;
use Imi\Test\Component\RequestContextProxy\RequestContextProxyA;
use Imi\Test\Component\RequestContextProxy\RequestContextProxyA2;

/**
 * @testdox RequestContextProxy
 */
class RequestContextProxyTest extends BaseTest
{
    public function testRequestContextProxy()
    {
        $a = new A();
        RequestContext::set('testRequestContextProxyA', $a);
        $this->assertEquals(3, RequestContextProxyA::add(1, 2));
        $this->assertEquals(5, (new RequestContextProxyA())->add(2, 3));
    }

    public function testBind()
    {
        try
        {
            RequestContextProxyA2::add(1, 2);
            $this->assertTrue(false);
        }
        catch (\Throwable $th)
        {
            $this->assertTrue(true);
        }
        RequestContextProxyA2::__bindProxy(RequestContextProxyA2::class, 'testRequestContextProxyA');
        $this->assertEquals(3, RequestContextProxyA2::add(1, 2));
        $this->assertEquals(5, (new RequestContextProxyA2())->add(2, 3));
    }
}
