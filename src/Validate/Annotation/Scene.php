<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;

/**
 * 场景定义.
 *
 * @Annotation
 * @Target({"CLASS"})
 *
 * @property string $name   场景名称
 * @property array  $fields 需要验证的字段名列表
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Scene extends Base
{
    public function __construct(?array $__data = null, string $name = '', array $fields = [])
    {
        parent::__construct(...\func_get_args());
    }
}
