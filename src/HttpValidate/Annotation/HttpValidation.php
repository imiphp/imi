<?php

declare(strict_types=1);

namespace Imi\HttpValidate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 自动验证 Http 参数.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class HttpValidation extends Base
{
}
