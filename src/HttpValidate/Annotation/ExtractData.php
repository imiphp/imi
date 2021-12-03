<?php

declare(strict_types=1);

namespace Imi\HttpValidate\Annotation;

use Imi\Bean\Annotation;

/**
 * 导出数据，兼容写法，不推荐使用该注解类.
 *
 * 请使用注解类：Imi\Server\Http\Annotation\ExtractData
 *
 * @Annotation
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class ExtractData extends \Imi\Server\Http\Annotation\ExtractData
{
}
