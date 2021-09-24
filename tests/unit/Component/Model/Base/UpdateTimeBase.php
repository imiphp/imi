<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model as Model;

/**
 * tb_update_time 基类.
 *
 * @Entity
 * @Table(name="tb_update_time", id={"id"})
 * @DDL(sql="CREATE TABLE `tb_update_time` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `date` date DEFAULT NULL,   `time` time DEFAULT NULL,   `datetime` datetime DEFAULT NULL,   `timestamp` timestamp NULL DEFAULT NULL,   `int` int(11) DEFAULT NULL,   `bigint` bigint(20) DEFAULT NULL,   `year` year(4) DEFAULT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT", decode="")
 *
 * @property int|null $id 
 * @property string|null $date 
 * @property string|null $time 
 * @property string|null $datetime 
 * @property string|null $timestamp 
 * @property int|null $int 
 * @property int|null $bigint 
 * @property int|null $year 
 */
abstract class UpdateTimeBase extends Model
{
    /**
     * id.
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
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
     * @param int|null $id id
     * @return static
     */
    public function setId($id)
    {
        $this->id = null === $id ? null : (int)$id;
        return $this;
    }

    /**
     * date.
     * @Column(name="date", type="date", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string|null
     */
    protected ?string $date = NULL;

    /**
     * 获取 date.
     *
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * 赋值 date.
     * @param string|null $date date
     * @return static
     */
    public function setDate($date)
    {
        $this->date = null === $date ? null : (string)$date;
        return $this;
    }

    /**
     * time.
     * @Column(name="time", type="time", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string|null
     */
    protected ?string $time = NULL;

    /**
     * 获取 time.
     *
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * 赋值 time.
     * @param string|null $time time
     * @return static
     */
    public function setTime($time)
    {
        $this->time = null === $time ? null : (string)$time;
        return $this;
    }

    /**
     * datetime.
     * @Column(name="datetime", type="datetime", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string|null
     */
    protected ?string $datetime = NULL;

    /**
     * 获取 datetime.
     *
     * @return string|null
     */
    public function getDatetime(): ?string
    {
        return $this->datetime;
    }

    /**
     * 赋值 datetime.
     * @param string|null $datetime datetime
     * @return static
     */
    public function setDatetime($datetime)
    {
        $this->datetime = null === $datetime ? null : (string)$datetime;
        return $this;
    }

    /**
     * timestamp.
     * @Column(name="timestamp", type="timestamp", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string|null
     */
    protected ?string $timestamp = NULL;

    /**
     * 获取 timestamp.
     *
     * @return string|null
     */
    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    /**
     * 赋值 timestamp.
     * @param string|null $timestamp timestamp
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = null === $timestamp ? null : (string)$timestamp;
        return $this;
    }

    /**
     * int.
     * @Column(name="int", type="int", length=11, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int|null
     */
    protected ?int $int = NULL;

    /**
     * 获取 int.
     *
     * @return int|null
     */
    public function getInt(): ?int
    {
        return $this->int;
    }

    /**
     * 赋值 int.
     * @param int|null $int int
     * @return static
     */
    public function setInt($int)
    {
        $this->int = null === $int ? null : (int)$int;
        return $this;
    }

    /**
     * bigint.
     * @Column(name="bigint", type="bigint", length=20, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int|null
     */
    protected ?int $bigint = NULL;

    /**
     * 获取 bigint.
     *
     * @return int|null
     */
    public function getBigint(): ?int
    {
        return $this->bigint;
    }

    /**
     * 赋值 bigint.
     * @param int|null $bigint bigint
     * @return static
     */
    public function setBigint($bigint)
    {
        $this->bigint = null === $bigint ? null : (int)$bigint;
        return $this;
    }

    /**
     * year.
     * @Column(name="year", type="year", length=4, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var int|null
     */
    protected ?int $year = NULL;

    /**
     * 获取 year.
     *
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * 赋值 year.
     * @param int|null $year year
     * @return static
     */
    public function setYear($year)
    {
        $this->year = null === $year ? null : (int)$year;
        return $this;
    }

}
