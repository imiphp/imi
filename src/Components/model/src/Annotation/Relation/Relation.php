<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Model\Enum\RelationPoolName;

/**
 * 自定义属性关联.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends RelationBase
{
    public function __construct(
        /**
         * 关联预加载查询.
         */
        public bool $with = true,
        /**
         * 设置结果模型的序列化字段.
         */
        public ?array $withFields = null,
        /**
         * 连接池名称，或 \Imi\Model\Enum\RelationPoolName 中的常量.
         *
         * @var int|string
         */
        public $poolName = RelationPoolName::PARENT
    ) {
    }
}
