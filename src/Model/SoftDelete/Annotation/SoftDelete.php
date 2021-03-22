<?php

declare(strict_types=1);

namespace Imi\Model\SoftDelete\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 软删除.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
class SoftDelete extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'field';

    /**
     * 软删除字段名.
     */
    public string $field = 'delete_time';

    /**
     * 软删除字段的默认值，代表非删除状态
     *
     * @var mixed
     */
    public $default = 0;

    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $field = 'delete_time', $default = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
