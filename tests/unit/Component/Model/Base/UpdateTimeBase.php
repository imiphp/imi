<?php

namespace Imi\Test\Component\Model\Base;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use Imi\Model\Model;

/**
 * tb_update_time 基类.
 *
 * @Entity
 * @Table(name="tb_update_time", id={"id"})
 * @DDL("CREATE TABLE `tb_update_time` (   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,   `date` date DEFAULT NULL,   `time` time DEFAULT NULL,   `datetime` datetime DEFAULT NULL,   `timestamp` timestamp NULL DEFAULT NULL,   `int` int(11) DEFAULT NULL,   `bigint` bigint(20) DEFAULT NULL,   `year` year(4) DEFAULT NULL,   PRIMARY KEY (`id`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT")
 *
 * @property int    $id
 * @property string $date
 * @property string $time
 * @property string $datetime
 * @property string $timestamp
 * @property int    $int
 * @property int    $bigint
 * @property int    $year
 */
abstract class UpdateTimeBase extends Model
{
    /**
     * id.
     *
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     *
     * @var int
     */
    protected $id;

    /**
     * 获取 id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id.
     *
     * @param int $id id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * date.
     *
     * @Column(name="date", type="date", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $date;

    /**
     * 获取 date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * 赋值 date.
     *
     * @param string $date date
     *
     * @return static
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * time.
     *
     * @Column(name="time", type="time", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $time;

    /**
     * 获取 time.
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * 赋值 time.
     *
     * @param string $time time
     *
     * @return static
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * datetime.
     *
     * @Column(name="datetime", type="datetime", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $datetime;

    /**
     * 获取 datetime.
     *
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * 赋值 datetime.
     *
     * @param string $datetime datetime
     *
     * @return static
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * timestamp.
     *
     * @Column(name="timestamp", type="timestamp", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var string
     */
    protected $timestamp;

    /**
     * 获取 timestamp.
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * 赋值 timestamp.
     *
     * @param string $timestamp timestamp
     *
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * int.
     *
     * @Column(name="int", type="int", length=11, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $int;

    /**
     * 获取 int.
     *
     * @return int
     */
    public function getInt()
    {
        return $this->int;
    }

    /**
     * 赋值 int.
     *
     * @param int $int int
     *
     * @return static
     */
    public function setInt($int)
    {
        $this->int = $int;

        return $this;
    }

    /**
     * bigint.
     *
     * @Column(name="bigint", type="bigint", length=20, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $bigint;

    /**
     * 获取 bigint.
     *
     * @return int
     */
    public function getBigint()
    {
        return $this->bigint;
    }

    /**
     * 赋值 bigint.
     *
     * @param int $bigint bigint
     *
     * @return static
     */
    public function setBigint($bigint)
    {
        $this->bigint = $bigint;

        return $this;
    }

    /**
     * year.
     *
     * @Column(name="year", type="year", length=4, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     *
     * @var int
     */
    protected $year;

    /**
     * 获取 year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * 赋值 year.
     *
     * @param int $year year
     *
     * @return static
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }
}
