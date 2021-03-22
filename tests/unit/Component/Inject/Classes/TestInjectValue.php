<?php

declare(strict_types=1);

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

    public function test(): void
    {
        Assert::assertNotNull($this->testLogic);
        $this->testLogic->test();
    }

    public function test2(): void
    {
        Assert::assertNotNull($this->testLogic2);
        $this->testLogic2->test();
    }
}
