<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 列字段注解.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Column extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 字段名.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * 字段类型.
     *
     * @var string|null
     */
    public ?string $type = null;

    /**
     * 字段长度.
     *
     * @var int
     */
    public int $length = -1;

    /**
     * 是否允许为null.
     *
     * @var bool
     */
    public bool $nullable = true;

    /**
     * 精度，小数位后几位.
     *
     * @var int
     */
    public int $accuracy = 0;

    /**
     * 默认值
     *
     * @var mixed
     */
    public $default;

    /**
     * 是否为主键.
     *
     * @var bool
     */
    public bool $isPrimaryKey = false;

    /**
     * 联合主键中的第几个，从0开始.
     *
     * @var int
     */
    public int $primaryKeyIndex = -1;

    /**
     * 是否为自增字段.
     *
     * @var bool
     */
    public bool $isAutoIncrement = false;

    /**
     * 虚拟字段，不参与数据库操作.
     *
     * @var bool
     */
    public bool $virtual = false;

    /**
     * save/update 模型时是否将当前时间写入该字段
     * 支持 date/time/datetime/timestamp/year/int/bigint
     * 当字段为 int 类型，写入秒级时间戳
     * 当字段为 bigint 类型，写入毫秒级时间戳.
     *
     * @var bool
     */
    public bool $updateTime = false;

    /**
     * 列表分割字符串.
     *
     * 如果字段类型为list，并且此字段不为null，读取时会处理为数组，写入时会处理为字符串
     *
     * @var string|null
     */
    public ?string $listSeparator = null;
}
