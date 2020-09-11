<?php

namespace Imi\Test\Component\Validate\Classes;

use Imi\Util\LazyArrayObject;
use Imi\Validate\Annotation\AutoValidation;
use Imi\Validate\Annotation\Compare;
use Imi\Validate\Annotation\Decimal;
use Imi\Validate\Annotation\InEnum;
use Imi\Validate\Annotation\InList;
use Imi\Validate\Annotation\Integer;
use Imi\Validate\Annotation\Number;
use Imi\Validate\Annotation\Required;
use Imi\Validate\Annotation\Text;
use Imi\Validate\Annotation\ValidateValue;

/**
 * @AutoValidation
 *
 * @Compare(name="compare", operation="<", value=0)
 * @Decimal(name="decimal", min=1, max=10, accuracy=2)
 * @InEnum(name="enum", enum=\Imi\Test\Component\Enum\TestEnum::class)
 * @InList(name="in", list={1, 2, 3}, message="{:value} 不在列表内")
 * @Required(name="required", message="{name}为必须参数")
 * @Number(name="number", min=0.01, max=999.99, accuracy=2, message="数值必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}")
 * @Text(name="text", min=6, max=12, message="{name}参数长度必须>={min} && <={max}")
 * @Compare(name="validateValue", value=@ValidateValue("{:data.compare}"), operation="==")
 */
class TestAutoConstructValidator extends LazyArrayObject
{
    /**
     * @Integer(min=0, max=100, message="{:value} 不符合大于等于{min}且小于等于{max}")
     *
     * @var int
     */
    public $int;

    /**
     * @AutoValidation
     *
     * @Integer(name="value", min=0, max=100, message="{:value} 不符合大于等于{min}且小于等于{max}")
     *
     * @param int $value
     *
     * @return void
     */
    public function test(int $value)
    {
        return $value;
    }
}
