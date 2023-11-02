<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * JSON 序列化时的配置.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class JsonEncode extends Base
{
    public function __construct(
        /**
         * json_encode() 的 flags 参数.
         */
        public int $flags = \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        /**
         * 递归层数.
         */
        public int $depth = 512
    ) {
    }
}
