<?php

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 工具操作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Tool\Parser\ToolParser")
 */
class Operation extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 操作名称.
     *
     * @var string
     */
    public $name;

    /**
     * 自动开启协程.
     *
     * @var bool
     */
    public $co = true;
}
