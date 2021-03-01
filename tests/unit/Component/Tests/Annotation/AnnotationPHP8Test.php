<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Annotation;

/**
 * @testdox Annotation PHP8
 */
class AnnotationPHP8Test extends AnnotationTest
{
    protected $className = \Imi\Test\Component\Annotation\TestAnnotationPHP8::class;

    protected $beanName = 'TestAnnotationPHP8';

    protected function setUp(): void
    {
        if (version_compare(\PHP_VERSION, '8.0', '<'))
        {
            $this->markTestSkipped();
        }
    }
}
