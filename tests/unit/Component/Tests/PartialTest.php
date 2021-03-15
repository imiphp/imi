<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use ReflectionClass;

/**
 * @testdox Partial
 */
class PartialTest extends BaseTest
{
    /**
     * Partial.
     *
     * @return void
     */
    public function testPartial(): void
    {
        /** @var \Imi\Test\Component\Partial\Classes\PartialClassA $test */
        $test = App::getBean('PartialClassA');
        $ref = new ReflectionClass($test);
        $this->assertTrue(method_exists($test, 'test1'));
        $this->assertEquals(1, $test->test1());
        $this->assertTrue(method_exists($test, 'test2'));
        // @phpstan-ignore-next-line
        $this->assertEquals(2, $test->test2());
        $this->assertTrue(method_exists($test, 'test3'));
        // @phpstan-ignore-next-line
        $this->assertEquals(3, $test->test3());

        $ref = new ReflectionClass($test);
        $this->assertTrue($ref->hasProperty('test2Value'));
        $test2Value = $ref->getProperty('test2Value');
        $this->assertTrue($test2Value->isPublic());

        // @phpstan-ignore-next-line
        $this->assertEquals(2, $test->test2Value);
        $this->assertTrue($ref->hasProperty('test3Value'));
        $test2Value = $ref->getProperty('test3Value');
        $this->assertTrue($test2Value->isPrivate());
    }
}
