<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * Bean.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\BeanParser")
 *
 * @property string|null $name         Bean名称，留空则为当前类名（包含完整命名空间）
 * @property string      $instanceType 实例化类型，默认为单例模式
 * @property bool        $recursion    是否启用递归特性
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Bean extends Base
{
    /**
     * 单例模式.
     */
    public const INSTANCE_TYPE_SINGLETON = 'singleton';

    /**
     * 每次实例化.
     */
    public const INSTANCE_TYPE_EACH_NEW = 'eachNew';

    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, ?string $name = null, string $instanceType = self::INSTANCE_TYPE_SINGLETON, bool $recursion = true)
    {
        parent::__construct(...\func_get_args());
    }
}
