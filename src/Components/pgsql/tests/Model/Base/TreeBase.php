<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_tree 基类.
 *
 * @Entity
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\Tree.name", default="tb_tree"), id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\Tree.poolName"))
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
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0)
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
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * parent_id.

     *
     * @Column(name="parent_id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
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
    public function setParentId(?int $parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * name.

     *
     * @Column(name="name", type="varchar", length=0, accuracy=32, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
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
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }
}
