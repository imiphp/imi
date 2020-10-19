<?php

namespace Imi\HttpValidate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 自动验证 Http 参数.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class HttpValidation extends Base
{
}
