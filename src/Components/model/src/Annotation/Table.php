<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 表注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Table extends Base
{
    public function __construct(
        /**
         * 表名.
         */
        public ?string $name = null,
        /**
         * 数据库连接池名称.
         */
        public ?string $dbPoolName = null,
        /**
         * 主键，支持数组方式设置联合索引.
         *
         * @var string|array|null
         */
        public $id = null,
        /**
         * 是否使用表前缀
         */
        public bool $usePrefix = false
    ) {
    }
}
