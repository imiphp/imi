<?php

namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA2
    {
        private $test3Value = 3;

        public function test3()
        {
            return $this->test3Value;
        }
    }
}

namespace Imi\Test\Component\Partial\Classes
{
    if (false)
    {
        class PartialClassA
        {
            private $test3Value;

            public function test3()
            {
            }
        }
    }
}
