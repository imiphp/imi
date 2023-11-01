<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;

#[Bean(name: 'TestInjectValue')]
class TestInjectValue
{
    /**
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValueLogic
     */
    #[Inject(name: 'TestInjectValueLogicXXX')]
    protected $testLogic;

    /**
     * @var TestInjectValueLogic
     */
    #[Inject]
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
