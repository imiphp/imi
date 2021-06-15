<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_tree 基类.
 *
 * @Entity
 * @Table(name="tb_tree", id={"id"})
 * @DDL("CREATE TABLE `tb_tree` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `parent_id` int(10) unsigned NOT NULL,   `name` varchar(32) NOT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT; insert into `tb_tree` values('1','0','a'); insert into `tb_tree` values('2','0','b'); insert into `tb_tree` values('3','0','c'); insert into `tb_tree` values('4','1','a-1'); insert into `tb_tree` values('5','1','a-2'); insert into `tb_tree` values('6','4','a-1-1'); insert into `tb_tree` values('7','4','a-1-2'); insert into `tb_tree` values('8','2','b-1'); insert into `tb_tree` values('9','2','b-2'); ")
 *
 * @property int    $id
 * @property int    $parentId
 * @property string $name
 */
abstract class TreeBase extends Model
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
     * parent_id.
     *
     * @Column(name="parent_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $parentId;

    /**
     * 获取 parentId.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * 赋值 parentId.
     *
     * @param int $parentId parent_id
     *
     * @return static
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * name.
     *
     * @Column(name="name", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $name;

    /**
     * 获取 name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 赋值 name.
     *
     * @param string $name name
     *
     * @return static
     */
    public function setName($name)
    {
        if (isset($name[31]))
        {
            throw new \InvalidArgumentException('The maximum length of $name is 32');
        }
        $this->name = $name;

        return $this;
    }
}
