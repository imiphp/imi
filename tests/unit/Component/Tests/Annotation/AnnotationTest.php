<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Annotation;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Test\BaseTest;

/**
 * @testdox Annotation
 */
class AnnotationTest extends BaseTest
{
    protected $className = \Imi\Test\Component\Annotation\TestAnnotation::class;

    protected $beanName = 'TestAnnotation';

    public function testClassAnnotation()
    {
        $annotations = AnnotationManager::getClassAnnotations($this->className);
        /** @var \Imi\Bean\Annotation\Bean $bean */
        $bean = $annotations[0] ?? null;
        $this->assertNotNull($bean);
        $this->assertEquals($this->beanName, $bean->name);
    }

    public function testMethodAnnotation()
    {
        $annotations = AnnotationManager::getMethodAnnotations($this->className, 'test');
        /** @var \Imi\Server\Http\Route\Annotation\Route $route */
        $route = $annotations[0] ?? null;
        $this->assertNotNull($route);
        $this->assertEquals('/testAnnotation', $route->url);
    }

    public function testPropertyAnnotation()
    {
        $annotations = AnnotationManager::getPropertyAnnotations($this->className, 'errorLog');
        /** @var \Imi\Aop\Annotation\Inject $inject */
        $inject = $annotations[0] ?? null;
        $this->assertNotNull($inject);
        $this->assertEquals('ErrorLog', $inject->name);
    }

    public function testConstantAnnotation()
    {
        $annotations = AnnotationManager::getConstantAnnotations($this->className, 'CONST_VALUE');
        /** @var \Imi\Enum\Annotation\EnumItem $enumItem */
        $enumItem = $annotations[0] ?? null;
        $this->assertNotNull($enumItem);
        $this->assertEquals('test', $enumItem->text);
    }
}
