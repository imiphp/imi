<?php

declare(strict_types=1);

namespace Imi\Model\SoftDelete\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Config;

/**
 * 软删除.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @property string $field   软删除字段名
 * @property mixed  $default 软删除字段的默认值，代表非删除状态
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class SoftDelete extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'field';

    /**
     * @param mixed $default
     */
    public function __construct(?array $__data = null, string $field = '', $default = 0)
    {
        parent::__construct(...\func_get_args());
        if ('' === $this->field)
        {
            $this->field = Config::get('@app.model.softDelete.fields.deleteTime', 'delete_time');
        }
    }
}
