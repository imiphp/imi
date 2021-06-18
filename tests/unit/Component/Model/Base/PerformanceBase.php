<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_performance 基类.
 *
 * @Entity
 * @Table(name="tb_performance", id={"id"})
 * @DDL("CREATE TABLE `tb_performance` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `value` varchar(255) NOT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT")
 *
 * @property int    $id
 * @property string $value
 */
abstract class PerformanceBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     *
     * @var int
     */
    protected $id;

    /**
     * 获取 id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * value.
     *
     * @Column(name="value", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $value;

    /**
     * 获取 value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 赋值 value.
     *
     * @param string $value value
     *
     * @return static
     */
    public function setValue($value)
    {
        if (mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = $value;

        return $this;
    }
}
