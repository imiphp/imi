<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 内存表注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string $name               表名
 * @property int    $size               指定表格的最大行数，如果$size不是为2的N次方，如1024、8192,65536等，底层会自动调整为接近的一个数字，如果小于1024则默认成1024，即1024是最小值
 * @property float  $conflictProportion 冲突比例；table占用的内存总数为 (结构体长度 + KEY长度64字节 + 行尺寸$size) * (1.2预留20%作为hash冲突) * (列尺寸)，如果机器内存不足table会创建失败
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class MemoryTable extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, string $name = '', int $size = 1024, float $conflictProportion = 0.2)
    {
        parent::__construct(...\func_get_args());
    }
}
