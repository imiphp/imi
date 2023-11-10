<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Traits\TAutoInject;

class TestTAutoInject
{
    use TAutoInject;

    #[Inject(name: 'TestInjectValue')]
    protected TestInjectValue $testInjectValue;

    public function getTestInjectValue(): TestInjectValue
    {
        return $this->testInjectValue;
    }
}
