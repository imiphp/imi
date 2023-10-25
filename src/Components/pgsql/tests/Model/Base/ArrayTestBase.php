<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_array_test 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null                  $id
 * @property array<int>|null           $arr1
 * @property array<array<string>>|null $arr2
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_array_test', id: [
        'id',
    ])
]
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'id', type: 'int4', nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true)
    ]
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
    public function setId(mixed $id): self
    {
        $this->id = null === $id ? null : (int) $id;

        return $this;
    }

    /**
     * arr1.
     *
     * @var array<int>|null
     */
    #[
        \Imi\Model\Annotation\Column(name: 'arr1', type: 'int8', nullable: false, ndims: 1)
    ]
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
    public function setArr1(mixed $arr1): self
    {
        $this->arr1 = null === $arr1 ? null : $arr1;

        return $this;
    }

    /**
     * arr2.
     *
     * @var array<array<string>>|null
     */
    #[
        \Imi\Model\Annotation\Column(name: 'arr2', type: 'varchar', length: 255, nullable: false, ndims: 2)
    ]
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
    public function setArr2(mixed $arr2): self
    {
        $this->arr2 = null === $arr2 ? null : $arr2;

        return $this;
    }
}
