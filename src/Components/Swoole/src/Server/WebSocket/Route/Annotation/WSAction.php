<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Swoole\Server\WebSocket\Parser\WSControllerParser")
 */
class WSAction extends Base
{
}
