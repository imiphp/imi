<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 一对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string        $model      关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string|null   $order      排序规则字符串；例：age desc, id desc
 * @property string[]|null $fields     查询时指定字段
 * @property int|null      $limit      限制返回记录数量
 * @property bool          $with       关联预加载查询
 * @property string[]|null $withFields 设置结果模型的序列化字段
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToMany extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'model';

    public function __construct(?array $__data = null, string $model = '', ?string $order = null, ?array $fields = null, ?int $limit = null, bool $with = false, ?array $withFields = null)
    {
        parent::__construct(...\func_get_args());
    }
}
