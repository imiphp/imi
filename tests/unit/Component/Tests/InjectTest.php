<?php

namespace Imi\Test\Component\Tests;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Test\BaseTest;
use Imi\Test\Component\Inject\Classes\TestTAutoInject;
use PHPUnit\Framework\Assert;

/**
 * @Bean
 * @testdox Inject
 */
class InjectTest extends BaseTest
{
    public function testInject()
    {
        $testTAutoInject = new TestTAutoInject();
        $value = $testTAutoInject->getTestInjectValue();
        Assert::assertNotNull($value);
        $value->test();
    }

    public function testArg()
    {
        $test = App::getBean('TestArg');
        $test->test('123');
    }
}
