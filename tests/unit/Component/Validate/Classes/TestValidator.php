<?php

declare(strict_types=1);

namespace Imi\Test\Component\Validate\Classes;

use Imi\Validate\Annotation\Compare;
use Imi\Validate\Annotation\Decimal;
use Imi\Validate\Annotation\InEnum;
use Imi\Validate\Annotation\InList;
use Imi\Validate\Annotation\Integer;
use Imi\Validate\Annotation\Number;
use Imi\Validate\Annotation\Regex;
use Imi\Validate\Annotation\Required;
use Imi\Validate\Annotation\Text;
use Imi\Validate\Annotation\ValidateValue;
use Imi\Validate\Validator;

#[Compare(name: 'compare', operation: '<', value: 0, exception: 'InvalidArgumentException', exCode: 0)]
#[Decimal(name: 'decimal', min: 1, max: 10, accuracy: 2, exception: 'InvalidArgumentException', exCode: 0)]
#[InEnum(name: 'enum', enum: 'Imi\\Test\\Component\\Enum\\TestEnum', exception: 'InvalidArgumentException', exCode: 0)]
#[InList(name: 'in', list: [1, 2, 3], message: '{:value} 不在列表内', exception: 'InvalidArgumentException', exCode: 0)]
#[Integer(name: 'int', min: 0, max: 100, message: '{:value} 不符合大于等于{min}且小于等于{max}', exception: 'InvalidArgumentException', exCode: 0)]
#[Required(name: 'required', message: '{name}为必须参数', exception: 'InvalidArgumentException', exCode: 0)]
#[Number(name: 'number', min: 0.01, max: 999.99, accuracy: 2, message: '数值必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}', exception: 'InvalidArgumentException', exCode: 0)]
#[Text(name: 'text', min: 6, max: 12, message: '{name}参数长度必须>={min} && <={max}', exception: 'InvalidArgumentException', exCode: 0)]
#[Text(name: 'chars', char: true, min: 6, max: 12, message: '{name}参数长度必须>={min} && <={max}', exception: 'InvalidArgumentException', exCode: 0)]
#[Compare(name: 'validateValue', value: new ValidateValue(value: '{:data.compare}'), exception: 'InvalidArgumentException', exCode: 0)]
#[Integer(name: 'optional', min: 0, max: 100, message: '{:value} 不符合大于等于{min}且小于等于{max}', optional: true, exception: 'InvalidArgumentException', exCode: 0)]
#[Regex(name: 'regex', pattern: '/^\\d+$/', exception: 'InvalidArgumentException', exCode: 0)]
#[Integer(name: 'list1.*.id', min: 0, max: 10, message: '{:value} 不符合大于等于{min}且小于等于{max}', exception: 'InvalidArgumentException', exCode: 0)]
#[Integer(name: 'list2.*', min: 0, max: 10, message: '{:value} 不符合大于等于{min}且小于等于{max}', exception: 'InvalidArgumentException', exCode: 0)]
class TestValidator extends Validator
{
}
