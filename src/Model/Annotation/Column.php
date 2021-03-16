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
#[\Attribute]
class Column extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 字段名.
     */
    public ?string $name = null;

    /**
     * 字段类型.
     */
    public ?string $type = null;

    /**
     * 字段长度.
     */
    public int $length = -1;

    /**
     * 是否允许为null.
     */
    public bool $nullable = true;

    /**
     * 精度，小数位后几位.
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
     */
    public bool $isPrimaryKey = false;

    /**
     * 联合主键中的第几个，从0开始.
     */
    public int $primaryKeyIndex = -1;

    /**
     * 是否为自增字段.
     */
    public bool $isAutoIncrement = false;

    /**
     * 虚拟字段，不参与数据库操作.
     */
    public bool $virtual = false;

    /**
     * save/update 模型时是否将当前时间写入该字段
     * 支持 date/time/datetime/timestamp/year/int/bigint
     * 当字段为 int 类型，写入秒级时间戳
     * 当字段为 bigint 类型，写入毫秒级时间戳.
     */
    public bool $updateTime = false;

    /**
     * 列表分割字符串.
     *
     * 如果字段类型为list，并且此字段不为null，读取时会处理为数组，写入时会处理为字符串
     */
    public ?string $listSeparator = null;

    public function __construct(?array $__data = null, ?string $name = null, ?string $type = null, int $length = -1, bool $nullable = true, int $accuracy = 0, bool $isPrimaryKey = false, int $primaryKeyIndex = -1, bool $isAutoIncrement = false, bool $virtual = false, bool $updateTime = false, ?string $listSeparator = null)
    {
        parent::__construct(...\func_get_args());
    }
}
