<?php

namespace Imi\HttpValidate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 导出数据，兼容写法，不推荐使用该注解类.
 *
 * 请使用注解类：Imi\Server\Http\Annotation\ExtractData
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class ExtractData extends \Imi\Server\Http\Annotation\ExtractData
{
}
