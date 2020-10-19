<?php

namespace Imi\Server\Route\Annotation\Udp;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\Route\Parser\UdpControllerParser")
 */
class UdpRoute extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'condition';

    /**
     * 条件.
     *
     * @var array
     */
    public $condition = [];
}
