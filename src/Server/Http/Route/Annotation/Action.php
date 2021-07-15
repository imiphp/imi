<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Http\Parser\ControllerParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Action extends Base
{
}
