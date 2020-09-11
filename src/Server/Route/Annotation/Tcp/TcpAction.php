<?php

namespace Imi\Server\Route\Annotation\Tcp;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\TcpControllerParser")
 */
class TcpAction extends Base
{
}
