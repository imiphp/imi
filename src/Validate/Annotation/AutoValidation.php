<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 自动验证
 *
 * 作为类注解-在构造方法调用完成后验证，验证失败抛出异常
 * 作为方法注解-在方法被调用前验证，验证失败抛出异常
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class AutoValidation extends Base
{
}
