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
 * tb_performance 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Performance.name", default="tb_performance"), usePrefix=true, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Performance.poolName"))
 * @DDL(sql="CREATE TABLE `tb_performance` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `value` varchar(255) NOT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT", decode="")
 *
 * @property int|null    $id
 * @property string|null $value
 */
abstract class PerformanceBase extends Model
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
     * value.
     *
     * @Column(name="value", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $value = null;

    /**
     * 获取 value.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 赋值 value.
     *
     * @param string|null $value value
     *
     * @return static
     */
    public function setValue($value)
    {
        if (\is_string($value) && mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = null === $value ? null : (string) $value;

        return $this;
    }
}
