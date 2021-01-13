<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Swoole\Server\TcpServer\Parser\TcpControllerParser")
 */
class TcpAction extends Base
{
}
