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
        /**
         * @var int
         */
        public $test2Value = 2;

        public function test2()
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
            /**
             * @var int
             */
            public $test2Value;

            /**
             * @return void
             */
            public function test2()
            {
            }
        }
    }
}
