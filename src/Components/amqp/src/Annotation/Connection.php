<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 连接.
 *
 * @Annotation
 *
 * @Target({"CLASS"})
 *
 * @property string|null $poolName 连接池名称
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Connection extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'poolName';

    public function __construct(
        ?array $__data = null,
        ?string $poolName = null
    ) {
        parent::__construct(...\func_get_args());
    }
}
