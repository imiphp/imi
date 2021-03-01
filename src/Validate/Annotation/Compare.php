<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 比较验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class Compare extends Condition
{
    /**
     * 被比较值
     *
     * @var mixed
     */
    public $value;

    /**
     * 比较符，使用顺序：name代表的值->比较符->被比较值
     *
     * 允许使用：==、!=、===、!==、<、<=、>、>=
     *
     * @var string
     */
    public string $operation = '==';

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::compare';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public array $args = [
        '{:value}',
        '{operation}',
        '{value}',
    ];

    public function __construct(?array $__data = null, string $operation = '==', array $args = [
        '{:value}',
        '{operation}',
        '{value}',
    ])
    {
        parent::__construct(...\func_get_args());
    }
}
