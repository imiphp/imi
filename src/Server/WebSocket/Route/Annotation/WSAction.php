<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\WebSocket\Parser\WSControllerParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class WSAction extends Base
{
}
