<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 方法参数注入.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class InjectArg extends Base
{
    /**
     * 参数名.
     *
     * @var string
     */
    public string $name;

    /**
     * 注入的值
     *
     * @var mixed
     */
    public $value;
}
