<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Sql;
use Imi\Test\Component\Model\Base\MemberBase;

/**
 * Member.
 *
 * @Inherit
 * @Serializables(mode="deny", fields={"password"})
 */
class MemberWithSqlField extends MemberBase
{
    /**
     * @Column(name="a", virtual=true)
     * @Sql("1+1")
     *
     * @var int
     */
    public $test1;

    /**
     * @Column(virtual=true)
     * @Sql("2+2")
     *
     * @var int
     */
    public $test2;

    /**
     * Set the value of test1.
     *
     * @param int $test1
     *
     * @return self
     */
    public function setTest1(int $test1)
    {
        $this->test1 = $test1;

        return $this;
    }

    /**
     * Get the value of test1.
     *
     * @return int
     */
    public function getTest1()
    {
        return $this->test1;
    }

    /**
     * Set the value of test2.
     *
     * @param int $test2
     *
     * @return self
     */
    public function setTest2(int $test2)
    {
        $this->test2 = $test2;

        return $this;
    }

    /**
     * Get the value of test2.
     *
     * @return int
     */
    public function getTest2()
    {
        return $this->test2;
    }
}
