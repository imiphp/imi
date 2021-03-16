<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 控制器注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\TcpServer\Parser\TcpControllerParser")
 */
#[\Attribute]
class TcpController extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'prefix';

    /**
     * 是否为单例控制器.
     *
     * 默认为 null 时取 '@server.服务器名.controller.singleton'
     */
    public ?bool $singleton = null;

    public function __construct(?array $__data = null, ?bool $singleton = null)
    {
        parent::__construct(...\func_get_args());
    }
}
