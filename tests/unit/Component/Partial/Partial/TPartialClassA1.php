<?php
namespace Imi\Test\Component\Partial\Partial
{
    use Imi\Bean\Annotation\Partial;

    /**
     * @Partial(Imi\Test\Component\Partial\Classes\PartialClassA::class)
     */
    trait TPartialClassA1
    {
        public $test2Value = 2;

        public function test2()
        {
            return $this->test2Value;
        }

    }

}

namespace Imi\Test\Component\Partial\Classes
{
    if(false)
    {
        class PartialClassA
        {
            public $test2Value;
    
            public function test2()
            {
    
            }
        }
    }

}
