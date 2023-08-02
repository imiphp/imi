<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_no_inc_pk 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\NoIncPk.name", default="tb_no_inc_pk"), usePrefix=false, id={"a_id", "b_id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\NoIncPk.poolName"))
 *
 * @property int|null    $aId
 * @property int|null    $bId
 * @property string|null $value
 */
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
     *
     * @Column(name="a_id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=false, ndims=0, virtual=false)
     */
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
    public function setAId(?int $aId)
    {
        $this->aId = $aId;

        return $this;
    }

    /**
     * b_id.
     *
     * @Column(name="b_id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=2, isAutoIncrement=false, ndims=0, virtual=false)
     */
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
    public function setBId(?int $bId)
    {
        $this->bId = $bId;

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
    public function setValue(?string $value)
    {
        if (\is_string($value) && mb_strlen($value) > 255)
        {
            throw new \InvalidArgumentException('The maximum length of $value is 255');
        }
        $this->value = $value;

        return $this;
    }
}
