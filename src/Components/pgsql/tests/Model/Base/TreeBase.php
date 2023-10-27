<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_tree 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property int|null    $parentId
 * @property string|null $name
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_tree', id: [
        'id',
    ])
]
abstract class TreeBase extends Model
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
     * parent_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'parent_id', type: 'int4', nullable: false)
    ]
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
    public function setParentId(mixed $parentId): self
    {
        $this->parentId = null === $parentId ? null : (int) $parentId;

        return $this;
    }

    /**
     * name.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'name', type: 'varchar', length: 32, nullable: false)
    ]
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
    public function setName(mixed $name): self
    {
        if (\is_string($name) && mb_strlen($name) > 32)
        {
            throw new \InvalidArgumentException('The maximum length of $name is 32');
        }
        $this->name = null === $name ? null : $name;

        return $this;
    }
}
