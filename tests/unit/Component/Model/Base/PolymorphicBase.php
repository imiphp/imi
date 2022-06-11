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
 * tb_polymorphic 基类.
 *
 * @Entity(camel=true, bean=true)
 * @Table(name=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Polymorphic.name", default="tb_polymorphic"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Test\Component\Model\Polymorphic.poolName"))
 * @DDL(sql="CREATE TABLE `tb_polymorphic` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `type` int(10) unsigned NOT NULL,   `to_one` int(10) unsigned NOT NULL DEFAULT '0',   `to_many` int(10) unsigned NOT NULL DEFAULT '0',   `one_to_one` int(10) unsigned NOT NULL DEFAULT '0',   `one_to_many` int(10) unsigned NOT NULL DEFAULT '0',   `many_to_many` int(10) unsigned NOT NULL DEFAULT '0',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8", decode="")
 *
 * @property int|null $id
 * @property int|null $type
 * @property int|null $toOne
 * @property int|null $toMany
 * @property int|null $oneToOne
 * @property int|null $oneToMany
 * @property int|null $manyToMany
 */
abstract class PolymorphicBase extends Model
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
     * type.
     *
     * @Column(name="type", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $type = null;

    /**
     * 获取 type.
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * 赋值 type.
     *
     * @param int|null $type type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = null === $type ? null : (int) $type;

        return $this;
    }

    /**
     * to_one.
     *
     * @Column(name="to_one", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $toOne = 0;

    /**
     * 获取 toOne.
     */
    public function getToOne(): ?int
    {
        return $this->toOne;
    }

    /**
     * 赋值 toOne.
     *
     * @param int|null $toOne to_one
     *
     * @return static
     */
    public function setToOne($toOne)
    {
        $this->toOne = null === $toOne ? null : (int) $toOne;

        return $this;
    }

    /**
     * to_many.
     *
     * @Column(name="to_many", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $toMany = 0;

    /**
     * 获取 toMany.
     */
    public function getToMany(): ?int
    {
        return $this->toMany;
    }

    /**
     * 赋值 toMany.
     *
     * @param int|null $toMany to_many
     *
     * @return static
     */
    public function setToMany($toMany)
    {
        $this->toMany = null === $toMany ? null : (int) $toMany;

        return $this;
    }

    /**
     * one_to_one.
     *
     * @Column(name="one_to_one", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $oneToOne = 0;

    /**
     * 获取 oneToOne.
     */
    public function getOneToOne(): ?int
    {
        return $this->oneToOne;
    }

    /**
     * 赋值 oneToOne.
     *
     * @param int|null $oneToOne one_to_one
     *
     * @return static
     */
    public function setOneToOne($oneToOne)
    {
        $this->oneToOne = null === $oneToOne ? null : (int) $oneToOne;

        return $this;
    }

    /**
     * one_to_many.
     *
     * @Column(name="one_to_many", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $oneToMany = 0;

    /**
     * 获取 oneToMany.
     */
    public function getOneToMany(): ?int
    {
        return $this->oneToMany;
    }

    /**
     * 赋值 oneToMany.
     *
     * @param int|null $oneToMany one_to_many
     *
     * @return static
     */
    public function setOneToMany($oneToMany)
    {
        $this->oneToMany = null === $oneToMany ? null : (int) $oneToMany;

        return $this;
    }

    /**
     * many_to_many.
     *
     * @Column(name="many_to_many", type="int", length=10, accuracy=0, nullable=false, default="0", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, unsigned=true)
     */
    protected ?int $manyToMany = 0;

    /**
     * 获取 manyToMany.
     */
    public function getManyToMany(): ?int
    {
        return $this->manyToMany;
    }

    /**
     * 赋值 manyToMany.
     *
     * @param int|null $manyToMany many_to_many
     *
     * @return static
     */
    public function setManyToMany($manyToMany)
    {
        $this->manyToMany = null === $manyToMany ? null : (int) $manyToMany;

        return $this;
    }
}
