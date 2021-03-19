<?php

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;

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

    /**
     * @Inject
     *
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValueLogic
     */
    protected $testLogic2;

    /**
     * @return void
     */
    public function test()
    {
        Assert::assertNotNull($this->testLogic);
        $this->testLogic->test();
    }

    /**
     * @return void
     */
    public function test2()
    {
        Assert::assertNotNull($this->testLogic2);
        $this->testLogic2->test();
    }
}
