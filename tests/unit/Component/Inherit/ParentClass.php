<?php

declare(strict_types=1);

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
     * @Callback(class="Test", method="test")
     *
     * @var int
     */
    public $id;

    /**
     * @Column
     * @Callback(class="Test", method="test")
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
     */
    public function test(): void
    {
    }

    /**
     * @FilterArg
     * @Transaction
     */
    public function test2(): void
    {
    }

    /**
     * @FilterArg
     * @Transaction
     */
    public function test3(): void
    {
    }
}
