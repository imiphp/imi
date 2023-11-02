<?php

declare(strict_types=1);

namespace Imi\Model\SoftDelete\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Config;

/**
 * 软删除.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class SoftDelete extends Base
{
    public function __construct(
        /**
         * 软删除字段名.
         */
        public string $field = '',
        /**
         * 软删除字段的默认值，代表非删除状态
         *
         * @var mixed
         */
        public $default = 0
    ) {
        if ('' === $this->field)
        {
            $this->field = Config::get('@app.model.softDelete.fields.deleteTime', 'delete_time');
        }
    }
}
