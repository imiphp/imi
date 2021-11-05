<?php

declare(strict_types=1);

namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA2
    {
        private int $test3Value = 3;

        public function test3(): int
        {
            return $this->test3Value;
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
            // @phpstan-ignore-next-line
            private int $test3Value;

            public function test3(): int
            {
                return 0;
            }
        }
    }
}
