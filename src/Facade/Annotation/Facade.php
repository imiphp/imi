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
 *
 * @property string $class   类名，支持 Bean 名
 * @property bool   $request 为 true 时，使用当前请求上下文的 Bean 对象
 * @property array  $args    实例化参数
 */
#[\Attribute]
class Facade extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'class';

    public function __construct(?array $__data = null, string $class = '', bool $request = false, array $args = [])
    {
        parent::__construct(...\func_get_args());
    }
}
