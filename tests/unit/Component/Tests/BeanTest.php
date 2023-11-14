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
use Imi\Test\Component\Enum\TestEnumBean;
use Imi\Test\Component\Enum\TestEnumBeanBacked;
use Imi\Util\Imi;

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
        $f = static fn (): ?int => 0;
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('int|null', ReflectionUtil::getTypeComments($rf->getReturnType()));

        $f = static fn (): \stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('\stdClass', ReflectionUtil::getTypeComments($rf->getReturnType()));

        // @phpstan-ignore-next-line
        $f = static fn (): ?\stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('\stdClass|null', ReflectionUtil::getTypeComments($rf->getReturnType()));

        $mf = new \ReflectionMethod($this, 'test1');
        $this->assertEquals('\Imi\Test\Component\Tests\BeanTest', ReflectionUtil::getTypeComments($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = static function (): mixed {
            };
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('mixed', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('string|int', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $c = Imi::eval(<<<'CODE'
            return new class() {
                public function test(): static
                {
                    return $this;
                }
            };
            CODE);
            $rf = new \ReflectionMethod($c, 'test');
            $this->assertEquals('static', ReflectionUtil::getTypeComments($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.1', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function (): IteratorAggregate&Countable {
                return new \ArrayObject();
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('\IteratorAggregate&\Countable', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function (): never {

            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('never', ReflectionUtil::getTypeComments($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.2', '>='))
        {
            $f = Imi::eval(<<<'CODE'
            return function(): true {
                return true;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('true', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function(): false {
                return false;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('false', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function(): null {
                return null;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('null', ReflectionUtil::getTypeComments($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function(): (IteratorAggregate&Countable)|stdClass|null {
                return new \ArrayObject();
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('(\IteratorAggregate&\Countable)|\stdClass|null', ReflectionUtil::getTypeComments($rf->getReturnType()));
        }
    }

    public function testGetTypeCode(): void
    {
        // @phpstan-ignore-next-line
        $f = static fn (): ?int => 0;
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('?int', ReflectionUtil::getTypeCode($rf->getReturnType()));

        $f = static fn (): \stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('\stdClass', ReflectionUtil::getTypeCode($rf->getReturnType()));

        // @phpstan-ignore-next-line
        $f = static fn (): ?\stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertEquals('?\stdClass', ReflectionUtil::getTypeCode($rf->getReturnType()));

        $mf = new \ReflectionMethod($this, 'test1');
        $this->assertEquals('\Imi\Test\Component\Tests\BeanTest', ReflectionUtil::getTypeCode($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = static function (): mixed {
            };
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('mixed', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('string|int', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $c = Imi::eval(<<<'CODE'
            return new class() {
                public function test(): static
                {
                    return $this;
                }
            };
            CODE);
            $rf = new \ReflectionMethod($c, 'test');
            $this->assertEquals('static', ReflectionUtil::getTypeCode($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.1', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function (): IteratorAggregate&Countable {
                return new \ArrayObject();
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('\IteratorAggregate&\Countable', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function (): never {

            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('never', ReflectionUtil::getTypeCode($rf->getReturnType()));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.2', '>='))
        {
            $f = Imi::eval(<<<'CODE'
            return function(): true {
                return true;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('true', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function(): false {
                return false;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('false', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<'CODE'
            return function(): null {
                return null;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('null', ReflectionUtil::getTypeCode($rf->getReturnType()));

            $f = Imi::eval(<<<CODE
            return function(): (IteratorAggregate&Countable)|stdClass|null {
                return new \ArrayObject();
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertEquals('(\IteratorAggregate&\Countable)|\stdClass|null', ReflectionUtil::getTypeCode($rf->getReturnType()));
        }
    }

    public function testAllowsType(): void
    {
        // @phpstan-ignore-next-line
        $f = static fn (): ?int => 0;
        $rf = new \ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));

        $f = static fn (): \stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), \stdClass::class));

        // @phpstan-ignore-next-line
        $f = static fn (): ?\stdClass => new \stdClass();
        $rf = new \ReflectionFunction($f);
        $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), \stdClass::class));

        $mf = new \ReflectionMethod($this, 'test1');
        $this->assertTrue(ReflectionUtil::allowsType($mf->getReturnType(), self::class, self::class));
        $this->assertFalse(ReflectionUtil::allowsType($mf->getReturnType(), self::class));

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.0', '>='))
        {
            // @phpstan-ignore-next-line
            $f = static function (): mixed {
            };
            $rf = new \ReflectionFunction($f);
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'mixed'));

            $f = Imi::eval(<<<'CODE'
            return function (): int|string {
                return 0;
            };
            CODE);
            $rf = new \ReflectionFunction($f);
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
            $rf = new \ReflectionFunction($f);
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'ArrayObject'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'IteratorAggregate'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'Countable'));

            $f = Imi::eval(<<<'CODE'
            return function (): never {

            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));
        }

        // @phpstan-ignore-next-line
        if (version_compare(\PHP_VERSION, '8.2', '>='))
        {
            $f = Imi::eval(<<<CODE
            return function(): (IteratorAggregate&Countable)|stdClass|null {
                return new \ArrayObject();
            };
            CODE);
            $rf = new \ReflectionFunction($f);
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'IteratorAggregate'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'Countable'));
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'ArrayObject'));
            $this->assertTrue(ReflectionUtil::allowsType($rf->getReturnType(), 'stdClass'));
            $this->assertFalse(ReflectionUtil::allowsType($rf->getReturnType(), 'int'));
        }
    }

    public function testContainerGetBean(): void
    {
        $a = App::getBean(\stdClass::class);
        $b = App::getBean(\stdClass::class);
        $this->assertTrue($a === $b);

        $a = App::getBean('BeanNew');
        $b = App::getBean('BeanNew');
        $this->assertTrue($a !== $b);
    }

    public function testContainerNewInstance(): void
    {
        $a = App::newInstance(\stdClass::class);
        $b = App::newInstance(\stdClass::class);
        $this->assertTrue($a !== $b);
    }

    public function testContainerSet(): void
    {
        $object = new \stdClass();

        $container = App::getContainer();
        $container->set(__METHOD__, $object);

        $a = $container->get(__METHOD__);
        $b = $container->get(__METHOD__);
        $this->assertTrue($a === $b);
        $this->assertTrue($a === $object);
    }

    public function testContainerBindCallable(): void
    {
        $object = new \stdClass();

        $container = App::getContainer();
        $container->bindCallable(__METHOD__, static fn () => $object);

        $a = $container->get(__METHOD__);
        $b = $container->get(__METHOD__);
        $this->assertTrue($a === $b);
        $this->assertTrue($a === $object);
    }

    public function testReferenceBean(): void
    {
        /** @var \Imi\Test\Component\Bean\ReferenceBean $referenceBean */
        $referenceBean = App::getBean('ReferenceBean');
        $b = null;
        $referenceBean->testParams(123, $b);
        $this->assertEquals(124, $b); // aop 里会 + 1

        $referenceBean->testReturnValue()[] = 1;

        $list1 = &$referenceBean->testReturnValue();
        $this->assertCount(1, $list1);
        $list1[] = 2;
        $list2 = $referenceBean->testReturnValue();
        $this->assertEquals($list1, $list2);
    }

    public function testConstructorPropertyBean(): void
    {
        if (\PHP_VERSION_ID < 80000)
        {
            $this->markTestSkipped();
        }
        /** @var \Imi\Test\Component\Bean\ConstructorPropertyBean $bean */
        // @phpstan-ignore-next-line
        $bean = App::getBean('ConstructorPropertyBean');
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(\Imi\Test\Component\Bean\ConstructorPropertyBean::class, $bean);
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(BeanA::class, $bean->getBeanA());
    }

    public function testReadOnlyBean(): void
    {
        if (\PHP_VERSION_ID < 80200)
        {
            $this->markTestSkipped();
        }
        /** @var \Imi\Test\Component\Bean\ReadOnlyBean $bean */
        // @phpstan-ignore-next-line
        $bean = App::getBean('ReadOnlyBean');
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(\Imi\Test\Component\Bean\ReadOnlyBean::class, $bean);
        // @phpstan-ignore-next-line
        $this->assertEquals('ReadOnlyBean', $bean->test());
    }

    public function testEnumBean(): void
    {
        if (\PHP_VERSION_ID < 80100)
        {
            $this->markTestSkipped();
        }
        /** @var \Imi\Test\Component\Bean\EnumBean $bean */
        // @phpstan-ignore-next-line
        $bean = App::getBean('EnumBean1');
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(\Imi\Test\Component\Bean\EnumBean::class, $bean);
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBean::A, $bean->getEnum1());
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBeanBacked::B, $bean->getEnum2());
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBean::A, $bean->getEnum3());

        /** @var \Imi\Test\Component\Bean\EnumBean $bean */
        // @phpstan-ignore-next-line
        $bean = App::getBean('EnumBean2');
        // @phpstan-ignore-next-line
        $this->assertInstanceOf(\Imi\Test\Component\Bean\EnumBean::class, $bean);
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBean::B, $bean->getEnum1());
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBeanBacked::A, $bean->getEnum2());
        // @phpstan-ignore-next-line
        $this->assertEquals(TestEnumBeanBacked::B, $bean->getEnum3());
    }

    // @phpstan-ignore-next-line
    private function test1(): self
    {
        return $this;
    }
}
