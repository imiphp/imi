<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * JSON 序列化时的配置.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property int $flags json_encode() 的 flags 参数
 * @property int $depth 递归层数
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class JsonEncode extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'flags';

    public function __construct(?array $__data = null, int $flags = 0, int $depth = 512)
    {
        parent::__construct(...\func_get_args());
    }
}
