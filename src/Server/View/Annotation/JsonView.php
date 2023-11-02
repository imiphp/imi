<?php

declare(strict_types=1);

namespace Imi\Server\View\Annotation;

/**
 * JSON 视图配置注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class JsonView extends BaseViewOption
{
    public function __construct(
        /**
         * 由以下常量组成的二进制掩码：JSON_HEX_QUOT、JSON_HEX_TAG、JSON_HEX_AMP、JSON_HEX_APOS、JSON_NUMERIC_CHECK、JSON_PRETTY_PRINT、JSON_UNESCAPED_SLASHES、JSON_FORCE_OBJECT、JSON_PRESERVE_ZERO_FRACTION、JSON_UNESCAPED_UNICODE、JSON_PARTIAL_OUTPUT_ON_ERROR.
         */
        public int $options = \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE,
        /**
         * 设置最大深度。 必须大于0。
         */
        public int $depth = 512
    ) {
    }
}
