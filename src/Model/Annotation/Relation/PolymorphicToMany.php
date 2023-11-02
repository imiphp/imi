<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Model\Enum\RelationPoolName;

/**
 * 用于多态多对多关联被关联的模型中使用，查询对应的左侧模型列表.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class PolymorphicToMany extends RelationBase
{
    public function __construct(
        /**
         * 关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名.
         */
        public string $model = '',
        /**
         * 关联的模型用于关联的字段.
         */
        public string $modelField = '',
        /**
         * 当前模型用于关联的字段.
         */
        public string $field = '',
        /**
         * 多态类型字段名.
         */
        public string $type = '',
        /**
         * 多态类型字段值
         *
         * @var mixed
         */
        public $typeValue = null,
        /**
         * 中间表模型；可以是包含命名空间的完整类名；可以同命名空间下的类名.
         */
        public string $middle = '',
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
         * 中间表与模型类的关联字段.
         */
        public string $middleLeftField = '',
        /**
         * 中间表与当前类的关联字段.
         */
        public string $middleRightField = '',
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
