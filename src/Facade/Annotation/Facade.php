<?php

namespace Imi\Facade\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 门面定义.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class Facade extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'class';

    /**
     * 类名，支持 Bean 名.
     *
     * @var string
     */
    public $class;

    /**
     * 为 true 时，使用当前请求上下文的 Bean 对象
     *
     * @var bool
     */
    public $request = false;

    /**
     * 实例化参数.
     *
     * @var array
     */
    public $args = [];
}
