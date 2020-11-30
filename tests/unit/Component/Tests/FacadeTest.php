<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Facade\FacadeA;
use Imi\Test\Component\Facade\FacadeA2;

/**
 * @testdox Facade
 */
class FacadeTest extends BaseTest
{
    public function testFacade()
    {
        $this->assertEquals(3, FacadeA::add(1, 2));
    }

    public function testBind()
    {
        try
        {
            FacadeA2::add(1, 2);
            $this->assertTrue(false);
        }
        catch (\Throwable $th)
        {
            $this->assertTrue(true);
        }
        FacadeA2::__bindFacade(FacadeA2::class, 'FacadeA');
        $this->assertEquals(3, FacadeA2::add(1, 2));
    }
}
