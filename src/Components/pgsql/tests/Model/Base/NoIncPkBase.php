<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_no_inc_pk 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $aId
 * @property int|null    $bId
 * @property string|null $value
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_no_inc_pk', id: [
        'a_id',
        'b_id',
    ])
]
abstract class NoIncPkBase extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = 'a_id';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = ['a_id', 'b_id'];

    /**
     * a_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'a_id', type: 'int4', nullable: false, isPrimaryKey: true, primaryKeyIndex: 0)
    ]
    protected ?int $aId = null;

    /**
     * 获取 aId.
     */
    public function getAId(): ?int
    {
        return $this->aId;
    }

    /**
     * 赋值 aId.
     *
     * @param int|null $aId a_id
     *
     * @return static
     */
    public function setAId(mixed $aId): self
    {
        $this->aId = null === $aId ? null : (int) $aId;

        return $this;
    }

    /**
     * b_id.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'b_id', type: 'int4', nullable: false, isPrimaryKey: true, primaryKeyIndex: 1)
    ]
    protected ?int $bId = null;

    /**
     * 获取 bId.
     */
    public function getBId(): ?int
    {
        return $this->bId;
    }

    /**
     * 赋值 bId.
     *
     * @param int|null $bId b_id
     *
     * @return static
     */
    public function setBId(mixed $bId): self
    {
        $this->bId = null === $bId ? null : (int) $bId;

        return $this;
    }

    /**
     * value.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'value', type: 'varchar', length: 255, nullable: false)
    ]
    protected ?string $value = null;

    /**
     * 获取 value.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 赋值 value.
     *
     * @param string|null $value value
     *
     * @return static
     */
    public function setValue(mixed $value): self
    {
        if (\is_string($value) && mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = null === $value ? null : $value;

        return $this;
    }
}
