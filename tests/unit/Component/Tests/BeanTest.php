<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Bean\Exception\ContainerException;
use Imi\Test\BaseTest;
use Imi\Test\Component\Bean\BeanA;
use Imi\Test\Component\Bean\BeanB;
use Imi\Test\Component\Bean\BeanC;

/**
 * @testdox Bean
 */
class BeanTest extends BaseTest
{
    public function testEnv(): void
    {
        $this->assertInstanceOf(BeanA::class, App::getBean('BeanA'));
        $this->assertInstanceOf(BeanB::class, App::getBean('BeanB'));
        $this->assertInstanceOf(BeanC::class, App::getBean('BeanC'));
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('BeanNotFound not found');
        App::getBean('BeanNotFound');
    }
}
