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
 * @property string        $model  关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string|null   $order  排序规则字符串；例：age desc, id desc
 * @property string[]|null $fields 为查询出来的模型指定字段
 */
#[\Attribute]
class OneToMany extends RelationBase
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'model';

    public function __construct(?array $__data = null, string $model = '', ?string $order = null, ?array $fields = null)
    {
        parent::__construct(...\func_get_args());
    }
}
