<?php

declare(strict_types=1);

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
     */
    public function test(): void
    {
    }

    public function test2(): void
    {
    }

    /**
     * @Inherit
     */
    public function test3(): void
    {
    }
}
