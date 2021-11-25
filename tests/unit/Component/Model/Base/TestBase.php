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
 * tb_test 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Test.name", default="tb_test"), dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Test.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test` (   `a` double DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property float|null $a
 */
abstract class TestBase extends Model
{
    /**
     * a.
     *
     * @Column(name="a", type="double", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     */
    protected ?float $a = null;

    /**
     * 获取 a.
     */
    public function getA(): ?float
    {
        return $this->a;
    }

    /**
     * 赋值 a.
     *
     * @param float|null $a a
     *
     * @return static
     */
    public function setA($a)
    {
        $this->a = null === $a ? null : (float) $a;

        return $this;
    }
}
