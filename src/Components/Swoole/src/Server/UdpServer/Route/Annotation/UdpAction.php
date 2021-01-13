<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Swoole\Server\UdpServer\Parser\UdpControllerParser")
 */
class UdpAction extends Base
{
}
