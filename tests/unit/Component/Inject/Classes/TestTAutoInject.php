<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Traits\TAutoInject;

class TestTAutoInject
{
    use TAutoInject;

    /**
     * @var \Imi\Test\Component\Inject\Classes\TestInjectValue
     */
    #[Inject(name: 'TestInjectValue')]
    protected $testInjectValue;

    public function getTestInjectValue(): TestInjectValue
    {
        return $this->testInjectValue;
    }
}
