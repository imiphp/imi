<?php
namespace Imi\Test\Component\Inject\Classes;

use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Aop\Annotation\Inject;

/**
 * @Bean("TestInjectValue")
 */
class TestInjectValue
{
    /**
     * @Inject("TestInjectValueLogic")
     *
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValueLogic
     */
    protected $testLogic;

    public function test()
    {
        Assert::assertNotNull($this->testLogic);
        $this->testLogic->test();
    }
}