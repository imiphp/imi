<?php

declare(strict_types=1);

namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA1
    {
        public int $test2Value = 2;

        public function test2(): int
        {
            return $this->test2Value;
        }
    }
}

namespace Imi\Test\Component\Partial\Classes
{
    // @phpstan-ignore-next-line
    if (false)
    {
        class PartialClassA
        {
            public int $test2Value;

            public function test2(): int
            {
                return 0;
            }
        }
    }
}
