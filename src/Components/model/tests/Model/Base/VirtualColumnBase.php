<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_virtual_column 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null              $id
 * @property int|null              $amount
 * @property string|float|int|null $virtualAmount
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_virtual_column', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_virtual_column` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `amount` int(11) NOT NULL,   `virtual_amount` decimal(10,2) GENERATED ALWAYS AS ((`amount` / 100)) VIRTUAL NOT NULL,   PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci')
]
abstract class VirtualColumnBase extends Model
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
     * amount.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'amount', type: \Imi\Cli\ArgType::INT, length: 11, nullable: false)
    ]
    protected ?int $amount = null;

    /**
     * 获取 amount.
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * 赋值 amount.
     *
     * @param int|null $amount amount
     *
     * @return static
     */
    public function setAmount(mixed $amount): self
    {
        $this->amount = null === $amount ? null : (int) $amount;

        return $this;
    }

    /**
     * virtual_amount.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'virtual_amount', type: 'decimal', length: 10, nullable: false, accuracy: 2, virtual: true)
    ]
    protected string|float|int|null $virtualAmount = null;

    /**
     * 获取 virtualAmount.
     */
    public function getVirtualAmount(): string|float|int|null
    {
        return $this->virtualAmount;
    }

    /**
     * 赋值 virtualAmount.
     *
     * @param string|float|int|null $virtualAmount virtual_amount
     *
     * @return static
     */
    public function setVirtualAmount(mixed $virtualAmount): self
    {
        $this->virtualAmount = null === $virtualAmount ? null : $virtualAmount;

        return $this;
    }
}
