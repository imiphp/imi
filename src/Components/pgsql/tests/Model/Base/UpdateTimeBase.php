<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_update_time 基类.
 *
 * @Entity(camel=true, bean=true, incrUpdate=false)
 *
 * @Table(name=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\UpdateTime.name", default="tb_update_time"), usePrefix=false, id={"id"}, dbPoolName=@ConfigValue(name="@app.models.Imi\Pgsql\Test\Model\UpdateTime.poolName"))
 *
 * @property int|null    $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $timetz
 * @property string|null $time2
 * @property string|null $timetz2
 * @property string|null $timestamp
 * @property string|null $timestamptz
 * @property string|null $timestamp2
 * @property string|null $timestamptz2
 * @property int|null    $int
 * @property int|null    $bigint
 */
abstract class UpdateTimeBase extends Model
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
     * @Column(name="id", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=1, isAutoIncrement=true, ndims=0, virtual=false)
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
     * @Column(name="date", type="date", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
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
     * @Column(name="time", type="time", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
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
     * timetz.
     *
     * @Column(name="timetz", type="timetz", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $timetz = null;

    /**
     * 获取 timetz.
     */
    public function getTimetz(): ?string
    {
        return $this->timetz;
    }

    /**
     * 赋值 timetz.
     *
     * @param string|null $timetz timetz
     *
     * @return static
     */
    public function setTimetz(?string $timetz)
    {
        $this->timetz = $timetz;

        return $this;
    }

    /**
     * time2.
     *
     * @Column(name="time2", type="time", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $time2 = null;

    /**
     * 获取 time2.
     */
    public function getTime2(): ?string
    {
        return $this->time2;
    }

    /**
     * 赋值 time2.
     *
     * @param string|null $time2 time2
     *
     * @return static
     */
    public function setTime2(?string $time2)
    {
        $this->time2 = $time2;

        return $this;
    }

    /**
     * timetz2.
     *
     * @Column(name="timetz2", type="timetz", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $timetz2 = null;

    /**
     * 获取 timetz2.
     */
    public function getTimetz2(): ?string
    {
        return $this->timetz2;
    }

    /**
     * 赋值 timetz2.
     *
     * @param string|null $timetz2 timetz2
     *
     * @return static
     */
    public function setTimetz2(?string $timetz2)
    {
        $this->timetz2 = $timetz2;

        return $this;
    }

    /**
     * timestamp.
     *
     * @Column(name="timestamp", type="timestamp", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
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
     * timestamptz.
     *
     * @Column(name="timestamptz", type="timestamptz", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $timestamptz = null;

    /**
     * 获取 timestamptz.
     */
    public function getTimestamptz(): ?string
    {
        return $this->timestamptz;
    }

    /**
     * 赋值 timestamptz.
     *
     * @param string|null $timestamptz timestamptz
     *
     * @return static
     */
    public function setTimestamptz(?string $timestamptz)
    {
        $this->timestamptz = $timestamptz;

        return $this;
    }

    /**
     * timestamp2.
     *
     * @Column(name="timestamp2", type="timestamp", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $timestamp2 = null;

    /**
     * 获取 timestamp2.
     */
    public function getTimestamp2(): ?string
    {
        return $this->timestamp2;
    }

    /**
     * 赋值 timestamp2.
     *
     * @param string|null $timestamp2 timestamp2
     *
     * @return static
     */
    public function setTimestamp2(?string $timestamp2)
    {
        $this->timestamp2 = $timestamp2;

        return $this;
    }

    /**
     * timestamptz2.
     *
     * @Column(name="timestamptz2", type="timestamptz", length=6, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
     */
    protected ?string $timestamptz2 = null;

    /**
     * 获取 timestamptz2.
     */
    public function getTimestamptz2(): ?string
    {
        return $this->timestamptz2;
    }

    /**
     * 赋值 timestamptz2.
     *
     * @param string|null $timestamptz2 timestamptz2
     *
     * @return static
     */
    public function setTimestamptz2(?string $timestamptz2)
    {
        $this->timestamptz2 = $timestamptz2;

        return $this;
    }

    /**
     * int.
     *
     * @Column(name="int", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
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
     * @Column(name="bigint", type="int8", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, virtual=false)
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
