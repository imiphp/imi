<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_performance 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name="tb_performance", usePrefix=false, id={"id"}, dbPoolName=null)
 *
 * @property int|null    $id
 * @property string|null $value
 */
abstract class PerformanceBase extends Model
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
     * value.
     *
     * @Column(name="value", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
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
    public function setValue($value)
    {
        if (\is_string($value) && mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = null === $value ? null : $value;

        return $this;
    }
}
