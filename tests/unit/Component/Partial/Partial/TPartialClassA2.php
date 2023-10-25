<?php

declare(strict_types=1);

namespace Imi\Test\Component\Partial\Partial;

use Imi\Bean\Annotation\Partial;

#[Partial(class: \Imi\Test\Component\Partial\Classes\PartialClassA::class)]
trait TPartialClassA2
{
    private int $test3Value = 3;

    public function test3(): int
    {
        return $this->test3Value;
    }
}
