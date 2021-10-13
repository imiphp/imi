<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\TcpServer\Parser\TcpControllerParser")
 *
 * @property array $condition 条件
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class TcpRoute extends Base implements \Stringable
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'condition';

    public function __toString()
    {
        return http_build_query($this->toArray());
    }

    public function __construct(?array $__data = null, array $condition = [])
    {
        parent::__construct(...\func_get_args());
    }
}
