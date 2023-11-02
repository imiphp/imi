<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 动作注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
#[Parser(className: \Imi\Server\WebSocket\Parser\WSControllerParser::class)]
class WSAction extends Base
{
}
