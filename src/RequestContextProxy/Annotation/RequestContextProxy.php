<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 请求上下文代理.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null $class 代理类名
 * @property string      $name  请求上下文中的名称
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RequestContextProxy extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, ?string $class = null, string $name = '')
    {
        parent::__construct(...\func_get_args());
    }
}
