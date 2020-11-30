<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 参数注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class Argument extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 参数名称.
     *
     * @var string
     */
    public string $name;

    /**
     * 参数类型.
     *
     * @var string
     */
    public ?string $type = null;

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
    public bool $required = false;

    /**
     * 注释.
     *
     * @var string
     */
    public string $comments = '';
}
