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
 */
#[\Attribute]
class OneToMany extends RelationBase
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'model';

    /**
     * 关联的模型类
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     */
    public string $model = '';

    /**
     * 排序规则字符串.
     *
     * 例：age desc, id desc
     */
    public ?string $order = null;

    public function __construct(?array $__data = null, string $model = '', ?string $order = null)
    {
        parent::__construct(...\func_get_args());
    }
}
