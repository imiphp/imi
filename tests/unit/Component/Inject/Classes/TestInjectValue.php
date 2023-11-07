<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;

#[Bean(name: 'TestInjectValue')]
class TestInjectValue
{
    #[Inject(name: 'TestInjectValueLogicXXX')]
    protected \Imi\Test\Component\Inject\Classes\TestInjectValueLogic $testLogic;

    #[Inject]
    protected TestInjectValueLogic $testLogic2;

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
