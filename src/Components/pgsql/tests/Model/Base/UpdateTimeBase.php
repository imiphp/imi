<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_update_time 基类.
 *
 * @Entity
 * @Table(name="tb_update_time", id={"id"})
 *
 * @property int|null    $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $datetime
 * @property string|null $timestamp
 * @property int|null    $int
 * @property int|null    $bigint
 */
abstract class UpdateTimeBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0)
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
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * date.
     *
     * @Column(name="date", type="date", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?string $date = null;

    /**
     * 获取 date.
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * 赋值 date.
     *
     * @param string|null $date date
     *
     * @return static
     */
    public function setDate(?string $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * time.
     *
     * @Column(name="time", type="time", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?string $time = null;

    /**
     * 获取 time.
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * 赋值 time.
     *
     * @param string|null $time time
     *
     * @return static
     */
    public function setTime(?string $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * datetime.
     *
     * @Column(name="datetime", type="timestamp", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?string $datetime = null;

    /**
     * 获取 datetime.
     */
    public function getDatetime(): ?string
    {
        return $this->datetime;
    }

    /**
     * 赋值 datetime.
     *
     * @param string|null $datetime datetime
     *
     * @return static
     */
    public function setDatetime(?string $datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * timestamp.
     *
     * @Column(name="timestamp", type="timestamp", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?string $timestamp = null;

    /**
     * 获取 timestamp.
     */
    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    /**
     * 赋值 timestamp.
     *
     * @param string|null $timestamp timestamp
     *
     * @return static
     */
    public function setTimestamp(?string $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * int.
     *
     * @Column(name="int", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?int $int = null;

    /**
     * 获取 int.
     */
    public function getInt(): ?int
    {
        return $this->int;
    }

    /**
     * 赋值 int.
     *
     * @param int|null $int int
     *
     * @return static
     */
    public function setInt(?int $int)
    {
        $this->int = $int;

        return $this;
    }

    /**
     * bigint.
     *
     * @Column(name="bigint", type="int8", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0)
     */
    protected ?int $bigint = null;

    /**
     * 获取 bigint.
     */
    public function getBigint(): ?int
    {
        return $this->bigint;
    }

    /**
     * 赋值 bigint.
     *
     * @param int|null $bigint bigint
     *
     * @return static
     */
    public function setBigint(?int $bigint)
    {
        $this->bigint = $bigint;

        return $this;
    }
}
