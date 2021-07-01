<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 场景定义.
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 *
 * @property string $name   场景名称
 * @property array  $fields 需要验证的字段名列表
 */
#[\Attribute]
class Scene extends Base
{
    public function __construct(?array $__data = null, string $name = '', array $fields = [])
    {
        parent::__construct(...\func_get_args());
    }
}
