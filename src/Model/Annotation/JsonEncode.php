<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * JSON 序列化时的配置.
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 *
 * @property int $flags json_encode() 的 flags 参数
 * @property int $depth 递归层数
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class JsonEncode extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'flags';

    public function __construct(?array $__data = null, int $flags = \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE, int $depth = 512)
    {
        parent::__construct(...\func_get_args());
    }
}
