<?php

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 过滤方法参数注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class FilterArg extends Base
{
    /**
     * 参数名.
     *
     * @var string
     */
    public $name;

    /**
     * 过滤器.
     *
     * @var callable
     */
    public $filter;
}
