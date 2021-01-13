<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Swoole\Server\UdpServer\Parser\UdpControllerParser")
 */
class UdpRoute extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'condition';

    /**
     * 条件.
     *
     * @var array
     */
    public array $condition = [];

    public function __toString()
    {
        return http_build_query($this->toArray());
    }
}
