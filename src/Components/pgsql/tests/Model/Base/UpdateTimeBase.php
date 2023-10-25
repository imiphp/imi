<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model\Base;

use Imi\Pgsql\Model\PgModel as Model;

/**
 * tb_update_time 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
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
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_update_time', id: [
        'id',
    ])
]
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
     */
    #[
        \Imi\Model\Annotation\Column(name: 'id', type: 'int4', nullable: false, isPrimaryKey: true, primaryKeyIndex: 0, isAutoIncrement: true)
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
     * date.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'date', type: 'date', nullable: false)
    ]
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
    public function setDate(mixed $date): self
    {
        $this->date = null === $date ? null : $date;

        return $this;
    }

    /**
     * time.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'time', type: 'time', length: 6, nullable: false)
    ]
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
    public function setTime(mixed $time): self
    {
        $this->time = null === $time ? null : $time;

        return $this;
    }

    /**
     * timetz.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timetz', type: 'timetz', length: 6, nullable: false)
    ]
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
    public function setTimetz(mixed $timetz): self
    {
        $this->timetz = null === $timetz ? null : $timetz;

        return $this;
    }

    /**
     * time2.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'time2', type: 'time', length: 6, nullable: false)
    ]
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
    public function setTime2(mixed $time2): self
    {
        $this->time2 = null === $time2 ? null : $time2;

        return $this;
    }

    /**
     * timetz2.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timetz2', type: 'timetz', length: 6, nullable: false)
    ]
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
    public function setTimetz2(mixed $timetz2): self
    {
        $this->timetz2 = null === $timetz2 ? null : $timetz2;

        return $this;
    }

    /**
     * timestamp.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timestamp', type: 'timestamp', length: 6, nullable: false)
    ]
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
    public function setTimestamp(mixed $timestamp): self
    {
        $this->timestamp = null === $timestamp ? null : $timestamp;

        return $this;
    }

    /**
     * timestamptz.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timestamptz', type: 'timestamptz', length: 6, nullable: false)
    ]
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
    public function setTimestamptz(mixed $timestamptz): self
    {
        $this->timestamptz = null === $timestamptz ? null : $timestamptz;

        return $this;
    }

    /**
     * timestamp2.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timestamp2', type: 'timestamp', length: 6, nullable: false)
    ]
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
    public function setTimestamp2(mixed $timestamp2): self
    {
        $this->timestamp2 = null === $timestamp2 ? null : $timestamp2;

        return $this;
    }

    /**
     * timestamptz2.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timestamptz2', type: 'timestamptz', length: 6, nullable: false)
    ]
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
    public function setTimestamptz2(mixed $timestamptz2): self
    {
        $this->timestamptz2 = null === $timestamptz2 ? null : $timestamptz2;

        return $this;
    }

    /**
     * int.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'int', type: 'int4', nullable: false)
    ]
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
    public function setInt(mixed $int): self
    {
        $this->int = null === $int ? null : (int) $int;

        return $this;
    }

    /**
     * bigint.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'bigint', type: 'int8', nullable: false)
    ]
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
    public function setBigint(mixed $bigint): self
    {
        $this->bigint = null === $bigint ? null : (int) $bigint;

        return $this;
    }
}
