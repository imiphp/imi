<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Bean\Exception\ContainerException;
use Imi\Bean\ReflectionUtil;
use Imi\Test\BaseTest;
use Imi\Test\Component\Bean\BeanA;
use Imi\Test\Component\Bean\BeanB;
use Imi\Test\Component\Bean\BeanC;
use Imi\Util\Imi;
use ReflectionFunction;
use ReflectionMethod;

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

    public function testGetTypeComments(): void
    {
        // @phpstan-ignore-next-line
        $f = function (): ?int {
            return 0;
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('int|null', ReflectionUtil::getTypeComments($rf->getReturnType()));

        $f = function (): \stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('\stdClass', ReflectionUtil::getTypeComments($rf->getReturnType()));

        // @phpstan-ignore-next-line
        $f = function (): ?\stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('\stdClass|null', ReflectionUtil::getTypeComments($rf->getReturnType()));

        $mf = new ReflectionMethod($this, 'test1');
        $this->assertEquals('\Imi\Test\Component\Tests\BeanTest', ReflectionUtil::getTypeComments($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = function (): mixed {
            };
            $rf = new ReflectionFunction($f);
            $this->assertEquals('mixed', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('int|string', ReflectionUtil::getTypeComments($rf->getReturnType()));
        }
    }

    public function testGetTypeCode(): void
    {
        // @phpstan-ignore-next-line
        $f = function (): ?int {
            return 0;
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('?int', ReflectionUtil::getTypeCode($rf->getReturnType()));

        $f = function (): \stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('\stdClass', ReflectionUtil::getTypeCode($rf->getReturnType()));

        // @phpstan-ignore-next-line
        $f = function (): ?\stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertEquals('?\stdClass', ReflectionUtil::getTypeCode($rf->getReturnType()));

        $mf = new ReflectionMethod($this, 'test1');
        $this->assertEquals('\Imi\Test\Component\Tests\BeanTest', ReflectionUtil::getTypeCode($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = function (): mixed {
            };
            $rf = new ReflectionFunction($f);
            $this->assertEquals('mixed', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('int|string', ReflectionUtil::getTypeCode($rf->getReturnType()));
        }
    }

    public function testAllowsType(): void
    {
        // @phpstan-ignore-next-line
        $f = function (): ?int {
            return 0;
        };
        $rf = new ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));

        $f = function (): \stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), \stdClass::class));

        // @phpstan-ignore-next-line
        $f = function (): ?\stdClass {
            return new \stdClass();
        };
        $rf = new ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), \stdClass::class));

        $mf = new ReflectionMethod($this, 'test1');
        $this->assertTrue(ReflectionUtil::allowsType($mf->getReturnType(), self::class, self::class));
        $this->assertFalse(ReflectionUtil::allowsType($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = function (): mixed {
            };
            $rf = new ReflectionFunction($f);
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'mixed'));

            $f = Imi::eval(<<<CODE
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'int|string'));
        }
    }

    // @phpstan-ignore-next-line
    private function test1(): self
    {
        return $this;
    }
}
