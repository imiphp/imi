<?php

namespace Imi\Enum\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * enum 枚举项.
 *
 * @Annotation
 * @Target("CONST")
 * @Parser("Imi\Enum\Annotation\Parser\EnumParser")
 */
class EnumItem extends Base
{
    /**
     * 文本描述.
     *
     * @var string
     */
    public $text = '';

    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'text';
}
