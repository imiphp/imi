<?php

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * JSON 序列化时的配置.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Model\Parser\ModelParser")
 */
#[\Attribute]
class JsonEncode extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'flags';

    /**
     * json_encode() 的 flags 参数.
     *
     * @var int
     */
    public int $flags = 0;

    /**
     * 递归层数.
     *
     * @var int
     */
    public int $depth = 512;

    public function __construct(?array $__data = null, int $flags = 0, int $depth = 512)
    {
        parent::__construct(...\func_get_args());
    }
}
