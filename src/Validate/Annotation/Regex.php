<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Parser;

/**
 * 正则验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class Regex extends Condition
{
    /**
     * 正则表达式文本.
     *
     * @var string
     */
    public string $pattern = '';

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable = '\Imi\Validate\ValidatorHelper::regex';

    /**
     * 参数名数组.
     *
     * @var array
     */
    public array $args = [
        '{:value}',
        '{pattern}',
    ];
}
