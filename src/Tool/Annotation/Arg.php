<?php

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 参数操作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Tool\Parser\ToolParser")
 */
class Arg extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 参数名称.
     *
     * @var string
     */
    public $name;

    /**
     * 参数类型.
     *
     * @var string
     */
    public $type;

    /**
     * 默认值
     *
     * @var mixed
     */
    public $default;

    /**
     * 是否是必选参数.
     *
     * @var bool
     */
    public $required = false;

    /**
     * 注释.
     *
     * @var string
     */
    public $comments = '';
}
