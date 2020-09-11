<?php

namespace Imi\Test\Component\Inherit;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Table;

/**
 * @Inherit(annotation="Imi\Bean\Annotation\Bean")
 * @Table
 */
class TestClass extends ParentClass
{
    /**
     * @Inherit({"Imi\Model\Annotation\Column"})
     *
     * @var int
     */
    public $id;

    /**
     * @Inherit
     *
     * @var int
     */
    public $id2;

    /**
     * @Inherit("")
     */
    const CCC = 1;

    /**
     * @Inherit
     */
    const CCC2 = 1;

    /**
     * @Inherit({"Imi\Aop\Annotation\FilterArg"})
     *
     * @return void
     */
    public function test()
    {
    }

    public function test2()
    {
    }

    /**
     * @Inherit
     *
     * @return void
     */
    public function test3()
    {
    }
}
