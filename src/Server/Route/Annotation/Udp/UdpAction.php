<?php

namespace Imi\Server\Route\Annotation\Udp;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\UdpControllerParser")
 */
class UdpAction extends Base
{
}
