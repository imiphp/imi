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
 * tb_tree 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Tree.name", default="tb_tree"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Tree.poolName"))
 * @DDL(sql="CREATE TABLE `tb_tree` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `parent_id` int(10) unsigned NOT NULL,   `name` varchar(32) NOT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT; insert into `tb_tree` values(1,0,'a'); insert into `tb_tree` values(2,0,'b'); insert into `tb_tree` values(3,0,'c'); insert into `tb_tree` values(4,1,'a-1'); insert into `tb_tree` values(5,1,'a-2'); insert into `tb_tree` values(6,4,'a-1-1'); insert into `tb_tree` values(7,4,'a-1-2'); insert into `tb_tree` values(8,2,'b-1'); insert into `tb_tree` values(9,2,'b-2'); ", decode="")
 *
 * @property int|null    $id
 * @property int|null    $parentId
 * @property string|null $name
 */
abstract class TreeBase extends Model
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
     * parent_id.
     *
     * @Column(name="parent_id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $parentId = null;

    /**
     * 获取 parentId.
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * 赋值 parentId.
     *
     * @param int|null $parentId parent_id
     *
     * @return static
     */
    public function setParentId($parentId)
    {
        $this->parentId = null === $parentId ? null : (int) $parentId;

        return $this;
    }

    /**
     * name.
     *
     * @Column(name="name", type="varchar", length=32, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=false)
     */
    protected ?string $name = null;

    /**
     * 获取 name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 赋值 name.
     *
     * @param string|null $name name
     *
     * @return static
     */
    public function setName($name)
    {
        if (\is_string($name) && mb_strlen($name) > 32)
        {
            throw new \InvalidArgumentException('The maximum length of $name is 32');
        }
        $this->name = null === $name ? null : (string) $name;

        return $this;
    }
}
