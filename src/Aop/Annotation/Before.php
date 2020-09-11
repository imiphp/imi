<?php

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 前置操作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class Before extends Base
{
}
