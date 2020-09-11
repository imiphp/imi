<?php

namespace Imi\Test\Component\Tests;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Traits\TAutoInject;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @Bean
 * @testdox Inject
 */
class InjectTest extends BaseTest
{
    use TAutoInject;

    /**
     * @Inject("TestInjectValue")
     *
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValue
     */
    protected $testInjectValue;

    /**
     * @param string $name
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testInject()
    {
        Assert::assertNotNull($this->testInjectValue);
        $this->testInjectValue->test();
    }

    public function testArg()
    {
        $test = App::getBean('TestArg');
        $test->test('123');
    }
}
