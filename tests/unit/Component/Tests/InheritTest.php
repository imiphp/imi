<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Aop\Annotation\FilterArg;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Callback;
use Imi\Bean\Annotation\Inherit;
use Imi\Db\Annotation\Transaction;
use Imi\Enum\Annotation\EnumItem;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Table;
use Imi\Test\BaseTest;
use Imi\Test\Component\Inherit\TestClass;

/**
 * @testdox Inherit
 */
class InheritTest extends BaseTest
{
    public function testClass(): void
    {
        $annotations = AnnotationManager::getClassAnnotations(TestClass::class);
        $this->assertCount(3, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Table::class, $annotations[1]);
        $this->assertInstanceOf(Bean::class, $annotations[2]);
    }

    public function testMethod(): void
    {
        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test');
        $this->assertCount(2, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(FilterArg::class, $annotations[1]);

        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test2');
        $this->assertCount(0, $annotations);

        $annotations = AnnotationManager::getMethodAnnotations(TestClass::class, 'test3');
        $this->assertCount(3, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(FilterArg::class, $annotations[1]);
        $this->assertInstanceOf(Transaction::class, $annotations[2]);
    }

    public function testProperty(): void
    {
        $annotations = AnnotationManager::getPropertyAnnotations(TestClass::class, 'id');
        $this->assertCount(2, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Column::class, $annotations[1]);

        $annotations = AnnotationManager::getPropertyAnnotations(TestClass::class, 'id2');
        $this->assertCount(3, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(Column::class, $annotations[1]);
        $this->assertInstanceOf(Callback::class, $annotations[2]);
    }

    public function testConst(): void
    {
        $annotations = AnnotationManager::getConstantAnnotations(TestClass::class, 'CCC');
        $this->assertCount(1, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);

        $annotations = AnnotationManager::getConstantAnnotations(TestClass::class, 'CCC2');
        $this->assertCount(2, $annotations);
        $this->assertInstanceOf(Inherit::class, $annotations[0]);
        $this->assertInstanceOf(EnumItem::class, $annotations[1]);
    }
}
