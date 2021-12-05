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
            $this->assertEquals('string|int', ReflectionUtil::getTypeComments($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.1', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function (): IteratorAggregate&Countable {
                return new \ArrayObject();
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('\IteratorAggregate&\Countable', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function (): never {

            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('never', ReflectionUtil::getTypeComments($rf->getReturnType()));
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
            $this->assertEquals('string|int', ReflectionUtil::getTypeCode($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.1', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function (): IteratorAggregate&Countable {
                return new \ArrayObject();
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('\IteratorAggregate&\Countable', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function (): never {

            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertEquals('never', ReflectionUtil::getTypeCode($rf->getReturnType()));
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
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'string|int'));
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'string'));
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.1', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function (): IteratorAggregate&Countable {
                return new \ArrayObject();
            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'ArrayObject'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'IteratorAggregate'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'Countable'));

            $f = Imi::eval(<<<CODE
            return function (): never {

            };
            CODE);
            $rf = new ReflectionFunction($f);
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));
        }
    }

    // @phpstan-ignore-next-line
    private function test1(): self
    {
        return $this;
    }
}
