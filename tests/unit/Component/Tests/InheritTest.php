<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Bean\Annotation\Bean;
use Imi\Model\Annotation\Table;
use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Aop\Annotation\FilterArg;
use Imi\Bean\Annotation\Callback;
use Imi\Enum\Annotation\EnumItem;
use Imi\Db\Annotation\Transaction;
use Imi\Test\Component\Inherit\TestClass;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @testdox Inherit
 */
class InheritTest extends BaseTest
{
    public function testClass()
    {
        $annotations = AnnotationManager::getClassAnnotations(TestClass::class);
        $this->assertEquals(3, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Table::class, $annotations[1]);
        $this->assertInstanceOf(Bean::class, $annotations[2]);
    }

    public function testMethod()
    {
        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test');
        $this->assertEquals(2, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(FilterArg::class, $annotations[1]);

        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test2');
        $this->assertEquals(0, count($annotations));

        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test3');
        $this->assertEquals(3, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(FilterArg::class, $annotations[1]);
        $this->assertInstanceOf(Transaction::class, $annotations[2]);
    }

    public function testProperty()
    {
        $annotations = AnnotationManager::getPropertyAnnotations(TestClass::class, 'id');
        $this->assertEquals(2, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Column::class, $annotations[1]);

        $annotations = AnnotationManager::getPropertyAnnotations(TestClass::class, 'id2');
        $this->assertEquals(3, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Column::class, $annotations[1]);
        $this->assertInstanceOf(Callback::class, $annotations[2]);
    }

    public function testConst()
    {
        $annotations = AnnotationManager::getConstantAnnotations(TestClass::class, 'CCC');
        $this->assertEquals(1, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);

        $annotations = AnnotationManager::getConstantAnnotations(TestClass::class, 'CCC2');
        $this->assertEquals(2, count($annotations));
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(EnumItem::class, $annotations[1]);
    }

}
