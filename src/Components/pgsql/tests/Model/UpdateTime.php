<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Pgsql\Test\Model\Base\UpdateTimeBase;

/**
 * tb_update_time.
 *
 * @Inherit
 */
class UpdateTime extends UpdateTimeBase
{
    /**
     * date.
     *
     * @Column(name="date", type="date", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?string $date = null;

    /**
     * time.
     *
     * @Column(name="time", type="time", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?string $time = null;

    /**
     * datetime.
     *
     * @Column(name="datetime", type="timestamp", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?string $datetime = null;

    /**
     * timestamp.
     *
     * @Column(name="timestamp", type="timestamp", length=0, accuracy=2, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?string $timestamp = null;

    /**
     * int.
     *
     * @Column(name="int", type="int4", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?int $int = null;

    /**
     * bigint.
     *
     * @Column(name="bigint", type="int8", length=-1, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false, ndims=0, updateTime=true)
     */
    protected ?int $bigint = null;
}
