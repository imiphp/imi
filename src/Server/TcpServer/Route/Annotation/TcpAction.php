<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 动作注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
#[Parser(className: \Imi\Server\TcpServer\Parser\TcpControllerParser::class)]
class TcpAction extends Base
{
}
