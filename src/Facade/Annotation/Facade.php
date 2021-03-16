<?php

declare(strict_types=1);

namespace Imi\Facade\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 门面定义.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Facade extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'class';

    /**
     * 类名，支持 Bean 名.
     */
    public string $class = '';

    /**
     * 为 true 时，使用当前请求上下文的 Bean 对象
     */
    public bool $request = false;

    /**
     * 实例化参数.
     */
    public array $args = [];

    public function __construct(?array $__data = null, string $class = '', bool $request = false, array $args = [])
    {
        parent::__construct(...\func_get_args());
    }
}
