<?php

declare(strict_types=1);

namespace Imi\HttpValidate\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 自动验证 Http 参数.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class HttpValidation extends Base
{
}
