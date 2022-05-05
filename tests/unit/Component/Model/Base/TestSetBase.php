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
 * tb_test_set 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestSet.name", default="tb_test_set"), usePrefix=true, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestSet.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test_set` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `value1` set('a','b','c','''test''') NOT NULL DEFAULT '''test''',   `value2` set('1','2','3') NOT NULL DEFAULT '1,2',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null   $id
 * @property array|null $value1
 * @property array|null $value2
 */
abstract class TestSetBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, unsigned=true)
     */
    protected ?int $id = null;

    /**
     * 获取 id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int|null $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * value1.
     *
     * @Column(name="value1", type="set", length=0, accuracy=0, nullable=false, default="'test'", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?array $value1 = [
        0 => '\'test\'',
    ];

    /**
     * 获取 value1.
     */
    public function getValue1(): ?array
    {
        return $this->value1;
    }

    /**
     * 赋值 value1.
     *
     * @param array|null $value1 value1
     *
     * @return static
     */
    public function setValue1($value1)
    {
        $this->value1 = null === $value1 ? null : $value1;

        return $this;
    }

    /**
     * value2.
     *
     * @Column(name="value2", type="set", length=0, accuracy=0, nullable=false, default="1,2", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?array $value2 = [
        0 => '1',
        1 => '2',
    ];

    /**
     * 获取 value2.
     */
    public function getValue2(): ?array
    {
        return $this->value2;
    }

    /**
     * 赋值 value2.
     *
     * @param array|null $value2 value2
     *
     * @return static
     */
    public function setValue2($value2)
    {
        $this->value2 = null === $value2 ? null : $value2;

        return $this;
    }
}
