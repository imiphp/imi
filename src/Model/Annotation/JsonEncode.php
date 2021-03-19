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
class JsonEncode extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'flags';

    /**
     * json_encode() 的 flags 参数.
     *
     * @var int
     */
    public $flags = 0;

    /**
     * 递归层数.
     *
     * @var int
     */
    public $depth = 512;
}
