<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Model\Enum\RelationPoolName;

/**
 * 多态多对多关联.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class PolymorphicManyToMany extends ManyToMany
{
    public function __construct(
        public string $model = '',
        public string $middle = '',
        public string $rightMany = '',
        public ?string $order = null,
        public ?array $fields = null,
        /**
         * 右表多态类型字段名.
         */
        public string $type = '',
        /**
         * 右表多态类型字段值
         *
         * @var mixed
         */
        public $typeValue = null,
        /**
         * 限制返回记录数量.
         */
        public ?int $limit = null,
        /**
         * 关联预加载查询.
         */
        public bool $with = false,
        /**
         * 设置结果模型的序列化字段.
         */
        public ?array $withFields = null,
        /**
         * 查询结果是否包含被软删除的数据，仅查询有效。非软删除模型请勿设置为 true.
         */
        public bool $withSoftDelete = false,
        /**
         * 连接池名称，或 \Imi\Model\Enum\RelationPoolName 中的常量.
         *
         * @var int|string
         */
        public $poolName = RelationPoolName::PARENT
    ) {
    }
}
