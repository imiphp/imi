<?php

declare(strict_types=1);

namespace Imi\AMQP\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 连接.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Connection extends Base
{
    public function __construct(
        /**
         * 连接池名称.
         */
        public ?string $poolName = null
    ) {
    }
}
