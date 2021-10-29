<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * t 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\T.name", default="t"), dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\T.poolName"))
 * @DDL(sql="CREATE TABLE `t` (   `c1` varchar(20) DEFAULT NULL,   `c2` text CHARACTER SET latin1 COLLATE latin1_general_cs ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property string|null $c1
 * @property string|null $c2
 */
abstract class TBase extends Model
{
    /**
     * c1.
     *
     * @Column(name="c1", type="varchar", length=20, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     */
    protected ?string $c1 = null;

    /**
     * 获取 c1.
     */
    public function getC1(): ?string
    {
        return $this->c1;
    }

    /**
     * 赋值 c1.
     *
     * @param string|null $c1 c1
     *
     * @return static
     */
    public function setC1($c1)
    {
        if (\is_string($c1) && mb_strlen($c1) > 20)
        {
            throw new \InvalidArgumentException('The maximum length of $c1 is 20');
        }
        $this->c1 = null === $c1 ? null : (string) $c1;

        return $this;
    }

    /**
     * c2.
     *
     * @Column(name="c2", type="text", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     */
    protected ?string $c2 = null;

    /**
     * 获取 c2.
     */
    public function getC2(): ?string
    {
        return $this->c2;
    }

    /**
     * 赋值 c2.
     *
     * @param string|null $c2 c2
     *
     * @return static
     */
    public function setC2($c2)
    {
        if (\is_string($c2) && mb_strlen($c2) > 65535)
        {
            throw new \InvalidArgumentException('The maximum length of $c2 is 65535');
        }
        $this->c2 = null === $c2 ? null : (string) $c2;

        return $this;
    }
}
