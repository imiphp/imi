<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 实体注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property bool $camel 序列化时使用驼峰命名
 * @property bool $bean  模型对象是否作为 bean 类使用
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'camel';

    public function __construct(?array $__data = null, bool $camel = true, bool $bean = true)
    {
        parent::__construct(...\func_get_args());
    }
}
