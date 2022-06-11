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
 * tb_test_enum 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestEnum.name", default="tb_test_enum"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\TestEnum.poolName"))
 * @DDL(sql="CREATE TABLE `tb_test_enum` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `value1` enum('a','b','c','''test''') NOT NULL DEFAULT '''test''',   `value2` enum('1','2','3') NOT NULL DEFAULT '1',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null    $id
 * @property string|null $value1
 * @property string|null $value2
 */
abstract class TestEnumBase extends Model
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
     * @Column(name="value1", type="enum", length=0, accuracy=0, nullable=false, default="'test'", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $value1 = '\'test\'';

    /**
     * 获取 value1.
     */
    public function getValue1(): ?string
    {
        return $this->value1;
    }

    /**
     * 赋值 value1.
     *
     * @param string|null $value1 value1
     *
     * @return static
     */
    public function setValue1($value1)
    {
        $this->value1 = null === $value1 ? null : (string) $value1;

        return $this;
    }

    /**
     * value2.
     *
     * @Column(name="value2", type="enum", length=0, accuracy=0, nullable=false, default="1", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $value2 = '1';

    /**
     * 获取 value2.
     */
    public function getValue2(): ?string
    {
        return $this->value2;
    }

    /**
     * 赋值 value2.
     *
     * @param string|null $value2 value2
     *
     * @return static
     */
    public function setValue2($value2)
    {
        $this->value2 = null === $value2 ? null : (string) $value2;

        return $this;
    }
}
