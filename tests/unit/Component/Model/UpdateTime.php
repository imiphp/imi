<?php
namespace Imi\Test\Component\Model;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Model\Base\UpdateTimeBase;

/**
 * UpdateTime
 * @Entity
 * @Table(name="tb_update_time", id={"id"})
 */
class UpdateTime extends UpdateTimeBase
{
    /**
     * date
     * @Column(name="date", type="date", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var string
     */
    protected $date;

    /**
     * time
     * @Column(name="time", type="time", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var string
     */
    protected $time;


    /**
     * datetime
     * @Column(name="datetime", type="datetime", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var string
     */
    protected $datetime;

    /**
     * timestamp
     * @Column(name="timestamp", type="timestamp", length=0, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var string
     */
    protected $timestamp;

    /**
     * int
     * @Column(name="int", type="int", length=11, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var int
     */
    protected $int;

    /**
     * bigint
     * @Column(name="bigint", type="bigint", length=20, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var int
     */
    protected $bigint;

    /**
     * year
     * @Column(name="year", type="year", length=4, accuracy=0, nullable=true, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, updateTime=true)
     * @var int
     */
    protected $year;

}
