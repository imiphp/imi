<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_polymorphic 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null $id
 * @property int|null $type
 * @property int|null $toOne
 * @property int|null $toMany
 * @property int|null $oneToOne
 * @property int|null $oneToMany
 * @property int|null $manyToMany
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_polymorphic', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_polymorphic` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `type` int(10) unsigned NOT NULL,   `to_one` int(10) unsigned NOT NULL DEFAULT \'0\',   `to_many` int(10) unsigned NOT NULL DEFAULT \'0\',   `one_to_one` int(10) unsigned NOT NULL DEFAULT \'0\',   `one_to_many` int(10) unsigned NOT NULL DEFAULT \'0\',   `many_to_many` int(10) unsigned NOT NULL DEFAULT \'0\',   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class PolymorphicBase extends Model
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
     * type.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'type', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, unsigned: true)
    ]
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
    public function setType(mixed $type): self
    {
        $this->type = null === $type ? null : (int) $type;

        return $this;
    }

    /**
     * to_one.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'to_one', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setToOne(mixed $toOne): self
    {
        $this->toOne = null === $toOne ? null : (int) $toOne;

        return $this;
    }

    /**
     * to_many.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'to_many', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setToMany(mixed $toMany): self
    {
        $this->toMany = null === $toMany ? null : (int) $toMany;

        return $this;
    }

    /**
     * one_to_one.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'one_to_one', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setOneToOne(mixed $oneToOne): self
    {
        $this->oneToOne = null === $oneToOne ? null : (int) $oneToOne;

        return $this;
    }

    /**
     * one_to_many.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'one_to_many', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setOneToMany(mixed $oneToMany): self
    {
        $this->oneToMany = null === $oneToMany ? null : (int) $oneToMany;

        return $this;
    }

    /**
     * many_to_many.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'many_to_many', type: \Imi\Cli\ArgType::INT, length: 10, nullable: false, default: '0', unsigned: true)
    ]
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
    public function setManyToMany(mixed $manyToMany): self
    {
        $this->manyToMany = null === $manyToMany ? null : (int) $manyToMany;

        return $this;
    }
}
