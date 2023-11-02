<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Model\Enum\RelationPoolName;

/**
 * 多对多关联.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ManyToMany extends RelationBase
{
    public function __construct(
        /**
         * 关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名.
         */
        public string $model = '',
        /**
         * 中间表模型；可以是包含命名空间的完整类名；可以同命名空间下的类名.
         */
        public string $middle = '',
        /**
         * 属性名，赋值为关联的模型对象列表.
         */
        public string $rightMany = '',
        /**
         * 排序规则字符串；例：age desc, id desc.
         */
        public ?string $order = null,
        /**
         * 查询时指定字段.
         */
        public ?array $fields = null,
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
         * @var int|string|null
         */
        public $poolName = RelationPoolName::PARENT
    ) {
    }
}
