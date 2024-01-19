<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

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
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_tree` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `parent_id` int(10) unsigned NOT NULL,   `name` varchar(32) NOT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT; insert into `tb_tree` values(1,0,\'a\'); insert into `tb_tree` values(2,0,\'b\'); insert into `tb_tree` values(3,0,\'c\'); insert into `tb_tree` values(4,1,\'a-1\'); insert into `tb_tree` values(5,1,\'a-2\'); insert into `tb_tree` values(6,4,\'a-1-1\'); insert into `tb_tree` values(7,4,\'a-1-2\'); insert into `tb_tree` values(8,2,\'b-1\'); insert into `tb_tree` values(9,2,\'b-2\'); ')
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
        \Imi\Model\Annotation\Column(name: 'id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true, unsigned: true)
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
        \Imi\Model\Annotation\Column(name: 'parent_id', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, unsigned: true)
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
        $this->name = null === $name ? null : (string) $name;

        return $this;
    }
}
