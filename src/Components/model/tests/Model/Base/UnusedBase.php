<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_unused 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null $id
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_unused', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_unused` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8')
]
abstract class UnusedBase extends Model
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
}
