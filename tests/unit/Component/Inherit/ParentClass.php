<?php

namespace Imi\Test\Component\Inherit;

use Imi\Aop\Annotation\FilterArg;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Callback;
use Imi\Db\Annotation\Transaction;
use Imi\Enum\Annotation\EnumItem;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * @Entity
 * @Bean
 */
class ParentClass
{
    /**
     * @Column
     * @Callback
     *
     * @var int
     */
    public $id;

    /**
     * @Column
     * @Callback
     *
     * @var int
     */
    public $id2;

    /**
     * @EnumItem
     */
    const CCC = 1;

    /**
     * @EnumItem
     */
    const CCC2 = 1;

    /**
     * @FilterArg
     * @Transaction
     *
     * @return void
     */
    public function test()
    {
    }

    /**
     * @FilterArg
     * @Transaction
     *
     * @return void
     */
    public function test2()
    {
    }

    /**
     * @FilterArg
     * @Transaction
     *
     * @return void
     */
    public function test3()
    {
    }
}
