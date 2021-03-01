<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 可选项参数注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
#[\Attribute]
class Option extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 参数名称.
     *
     * @var string
     */
    public string $name = '';

    /**
     * 短名称.
     *
     * @var string|null
     */
    public ?string $shortcut = null;

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

    public function __construct(?array $__data = null, string $name = '', ?string $shortcut = null, ?string $type = null, bool $required = false, string $comments = '')
    {
        parent::__construct(...\func_get_args());
    }
}
