<?php

namespace Imi\Server\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\ControllerParser")
 */
class Action extends Base
{
}
