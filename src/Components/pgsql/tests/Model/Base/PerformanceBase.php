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
 * @Entity
 * @Table(name="tb_performance", id={"id"})
 *
 * @property int|null $id 
 * @property string|null $value 
 */
abstract class PerformanceBase extends Model
{
    /**
     * id.

     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0)
     * @var int|null
     */
    protected ?int $id = NULL;

    /**
     * 获取 id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int|null $id id
     * @return static
     */
    public function setId(?int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * value.

     * @Column(name="value", type="varchar", length=0, accuracy=255, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     * @var string|null
     */
    protected ?string $value = NULL;

    /**
     * 获取 value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 赋值 value.
     *
     * @param string|null $value value
     * @return static
     */
    public function setValue(?string $value)
    {
        $this->value = $value;
        return $this;
    }

}
