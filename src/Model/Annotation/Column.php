<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 列字段注解.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property string|null $name            字段名
 * @property string|null $type            字段类型
 * @property int         $length          字段长度
 * @property bool        $nullable        是否允许为null
 * @property int         $accuracy        精度，小数位后几位
 * @property mixed       $default         默认值
 * @property bool        $isPrimaryKey    是否为主键
 * @property int         $primaryKeyIndex 联合主键中的第几个，从0开始
 * @property bool        $isAutoIncrement 是否为自增字段
 * @property bool        $virtual         虚拟字段，不参与数据库操作
 * @property bool        $updateTime      save/update 模型时是否将当前时间写入该字段；支持 date/time/datetime/timestamp/year/int/bigint；当字段为 int 类型，写入秒级时间戳；当字段为 bigint 类型，写入毫秒级时间戳
 * @property string|null $listSeparator   列表分割字符串；如果字段类型为list，并且此字段不为null，读取时会处理为数组，写入时会处理为字符串
 * @property int         $ndims           数组维度，大于0则为数组字段
 * @property string      $reference       引用字段名，作为引用字段的别名使用，拥有同等的读写能力
 * @property bool        $unsigned        字段类型是否为无符号
 * @property bool        $createTime      save/create 模型时是否将当前时间写入该字段，save时表有自增ID主键才支持；支持 date/time/datetime/timestamp/year/int/bigint；当字段为 int 类型，写入秒级时间戳；当字段为 bigint 类型，写入毫秒级时间戳
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, ?string $name = null, ?string $type = null, int $length = -1, bool $nullable = true, int $accuracy = 0, $default = null, bool $isPrimaryKey = false, int $primaryKeyIndex = -1, bool $isAutoIncrement = false, bool $virtual = false, bool $updateTime = false, ?string $listSeparator = null, int $ndims = 0, string $reference = '', bool $unsigned = false, bool $createTime = false)
    {
        parent::__construct(...\func_get_args());
    }
}
