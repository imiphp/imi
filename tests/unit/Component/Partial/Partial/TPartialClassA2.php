<?php

namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA2
    {
        /**
         * @var int
         */
        private $test3Value = 3;

        /**
         * @return void
         */
        public function test3()
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
            /**
             * @var int
             */
            private $test3Value;

            /**
             * @return void
             */
            public function test3()
            {
            }
        }
    }
}
