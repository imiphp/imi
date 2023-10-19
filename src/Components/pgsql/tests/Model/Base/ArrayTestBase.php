<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_array_test 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\ArrayTest.name", default="tb_array_test"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\ArrayTest.poolName"))
 *
 * @property int|null                  $id
 * @property array<int>|null           $arr1
 * @property array<array<string>>|null $arr2
 */
abstract class ArrayTestBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['id'];

    /**
     * id.
     *
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true, ndims=0, virtual=false)
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
     * arr1.
     *
     * @Column(name="arr1", type="int8", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=1, virtual=false)
     *
     * @var array<int>|null
     */
    protected ?array $arr1 = null;

    /**
     * 获取 arr1.
     *
     * @return array<int>|null
     */
    public function getArr1(): ?array
    {
        return $this->arr1;
    }

    /**
     * 赋值 arr1.
     *
     * @param array<int>|null $arr1 arr1
     *
     * @return static
     */
    public function setArr1($arr1)
    {
        $this->arr1 = null === $arr1 ? null : $arr1;

        return $this;
    }

    /**
     * arr2.
     *
     * @Column(name="arr2", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=2, virtual=false)
     *
     * @var array<array<string>>|null
     */
    protected ?array $arr2 = null;

    /**
     * 获取 arr2.
     *
     * @return array<array<string>>|null
     */
    public function getArr2(): ?array
    {
        return $this->arr2;
    }

    /**
     * 赋值 arr2.
     *
     * @param array<array<string>>|null $arr2 arr2
     *
     * @return static
     */
    public function setArr2($arr2)
    {
        $this->arr2 = null === $arr2 ? null : $arr2;

        return $this;
    }
}
