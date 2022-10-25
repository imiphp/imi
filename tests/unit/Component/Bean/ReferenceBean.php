<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean(name="ReferenceBean")
 */
class ReferenceBean
{
    private array $list = [];

    public function testParams(int $a, ?int &$b): void
    {
        $b = $a;
    }

    public function &testReturnValue(): array
    {
        return $this->list;
    }
}
