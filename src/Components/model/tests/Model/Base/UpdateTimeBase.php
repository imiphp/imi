<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model\Base;

use Imi\Model\Model;

/**
 * tb_update_time 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
 * @property int|null    $id
 * @property string|null $date
 * @property string|null $time
 * @property string|null $datetime
 * @property string|null $timestamp
 * @property int|null    $int
 * @property int|null    $bigint
 * @property int|null    $bigintSecond
 * @property int|null    $year
 */
#[
    \Imi\Model\Annotation\Entity(),
    \Imi\Model\Annotation\Table(name: 'tb_update_time', id: [
        'id',
    ]),
    \Imi\Model\Annotation\DDL(sql: 'CREATE TABLE `tb_update_time` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `date` date DEFAULT NULL,   `time` time DEFAULT NULL,   `datetime` datetime DEFAULT NULL,   `timestamp` timestamp NULL DEFAULT NULL,   `int` int(11) DEFAULT NULL,   `bigint` bigint(20) DEFAULT NULL,   `bigint_second` bigint(20) DEFAULT NULL,   `year` year(4) DEFAULT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT')
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
     * date.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'date', type: 'date', length: 0)
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
        $this->date = null === $date ? null : (string) $date;

        return $this;
    }

    /**
     * time.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'time', type: 'time', length: 0)
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
        $this->time = null === $time ? null : (string) $time;

        return $this;
    }

    /**
     * datetime.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'datetime', type: 'datetime', length: 0)
    ]
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
    public function setDatetime(mixed $datetime): self
    {
        $this->datetime = null === $datetime ? null : (string) $datetime;

        return $this;
    }

    /**
     * timestamp.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'timestamp', type: 'timestamp', length: 0)
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
        $this->timestamp = null === $timestamp ? null : (string) $timestamp;

        return $this;
    }

    /**
     * int.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'int', type: \Imi\Cli\ArgType::INT, length: 11)
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
        \Imi\Model\Annotation\Column(name: 'bigint', type: 'bigint', length: 20)
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

    /**
     * bigint_second.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'bigint_second', type: 'bigint', length: 20)
    ]
    protected ?int $bigintSecond = null;

    /**
     * 获取 bigintSecond.
     */
    public function getBigintSecond(): ?int
    {
        return $this->bigintSecond;
    }

    /**
     * 赋值 bigintSecond.
     *
     * @param int|null $bigintSecond bigint_second
     *
     * @return static
     */
    public function setBigintSecond(mixed $bigintSecond): self
    {
        $this->bigintSecond = null === $bigintSecond ? null : (int) $bigintSecond;

        return $this;
    }

    /**
     * year.
     */
    #[
        \Imi\Model\Annotation\Column(name: 'year', type: 'year', length: 4)
    ]
    protected ?int $year = null;

    /**
     * 获取 year.
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * 赋值 year.
     *
     * @param int|null $year year
     *
     * @return static
     */
    public function setYear(mixed $year): self
    {
        $this->year = null === $year ? null : (int) $year;

        return $this;
    }
}
