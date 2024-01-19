<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Sql;
use Imi\Model\Test\Model\Base\MemberBase;

/**
 * Member.
 */
#[Inherit]
#[Serializables(mode: 'deny', fields: ['password'])]
class MemberWithSqlField extends MemberBase
{
    #[Column(name: 'a', virtual: true)]
    #[Sql(sql: '1+1')]
    public int $test1;

    #[Column(virtual: true)]
    #[Sql(sql: '2+2')]
    public int $test2;

    /**
     * Set the value of test1.
     */
    public function setTest1(int $test1): self
    {
        $this->test1 = $test1;

        return $this;
    }

    /**
     * Get the value of test1.
     */
    public function getTest1(): int
    {
        return $this->test1;
    }

    /**
     * Set the value of test2.
     */
    public function setTest2(int $test2): self
    {
        $this->test2 = $test2;

        return $this;
    }

    /**
     * Get the value of test2.
     */
    public function getTest2(): int
    {
        return $this->test2;
    }
}
