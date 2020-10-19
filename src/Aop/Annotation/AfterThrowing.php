<?php

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 在异常时通知.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class AfterThrowing extends Base
{
    /**
     * 允许捕获的异常类列表.
     *
     * @var array
     */
    public $allow = [];

    /**
     * 不允许捕获的异常类列表.
     *
     * @var array
     */
    public $deny = [];
}
