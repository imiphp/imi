<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class ManyToMany extends RelationBase
{
    /**
     * 关联的模型类
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     */
    public string $model = '';

    /**
     * 中间表模型
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     */
    public string $middle = '';

    /**
     * 属性名，赋值为关联的模型对象列表.
     */
    public string $rightMany = '';

    /**
     * 排序规则字符串.
     *
     * 例：age desc, id desc
     */
    public ?string $order = null;

    public function __construct(?array $__data = null, string $model = '', string $middle = '', string $rightMany = '', ?string $order = null)
    {
        parent::__construct(...\func_get_args());
    }
}
