<?php

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Traits\TAutoInject;

class TestTAutoInject
{
    use TAutoInject;

    /**
     * @Inject("TestInjectValue")
     *
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValue
     */
    protected $testInjectValue;

    public function getTestInjectValue(): TestInjectValue
    {
        return $this->testInjectValue;
    }
}
