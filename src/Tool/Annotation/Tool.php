<?php

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 工具注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Tool\Parser\ToolParser")
 */
class Tool extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 工具名称.
     *
     * @var string
     */
    public $name;
}
