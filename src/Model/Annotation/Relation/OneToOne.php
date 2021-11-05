<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 一对一关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string        $model  关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string[]|null $fields 为查询出来的模型指定字段
 * @property bool          $with   关联预加载查询
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToOne extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'model';

    public function __construct(?array $__data = null, string $model = '', ?array $fields = null, bool $with = false)
    {
        parent::__construct(...\func_get_args());
    }
}
