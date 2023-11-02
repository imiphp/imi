<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 请求上下文代理.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RequestContextProxy extends Base
{
    public function __construct(
        /**
         * 代理类名.
         */
        public ?string $class = null,
        /**
         * 请求上下文中的名称.
         */
        public string $name = ''
    ) {
    }
}
