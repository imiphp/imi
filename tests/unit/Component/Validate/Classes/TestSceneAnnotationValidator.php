<?php

namespace Imi\Test\Component\Validate\Classes;

use Imi\Validate\Annotation\Decimal;
use Imi\Validate\Annotation\Integer;
use Imi\Validate\Annotation\Scene;
use Imi\Validate\Validator;

/**
 * @Decimal(name="decimal", min=1, max=10, accuracy=2)
 * @Integer(name="int", min=0, max=100, message="{:value} 不符合大于等于{min}且小于等于{max}")
 * @Scene(name="a", fields={"decimal"})
 * @Scene(name="b", fields={"int"})
 * @Scene(name="c", fields={"decimal", "int"})
 */
class TestSceneAnnotationValidator extends Validator
{
}
