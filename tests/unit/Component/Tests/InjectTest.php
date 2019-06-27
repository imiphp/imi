<?php
namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\Inject;
use Imi\Bean\Traits\TAutoInject;

/**
 * @Bean
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