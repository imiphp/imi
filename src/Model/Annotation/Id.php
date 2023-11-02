<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 表主键声明.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id extends Base
{
    public function __construct(
        /**
         * 顺序。默认为 null 时以属性顺序为准；为 false 时表示不是主键，但 ID 生成器依然有效。
         *
         * @var int|false|null
         */
        public $index = null,
        /**
         * ID 生成器类名，如果是空字符串则不自动生成.
         */
        public string $generator = '',
        public array $generatorOptions = []
    ) {
    }
}
