<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * Bean.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\BeanParser")
 */
#[\Attribute]
class Bean extends Base
{
    /**
     * 单例模式.
     */
    const INSTANCE_TYPE_SINGLETON = 'singleton';

    /**
     * 每次实例化.
     */
    const INSTANCE_TYPE_EACH_NEW = 'eachNew';

    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * Bean名称，留空则为当前类名（包含完整命名空间）.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * 实例化类型，默认为单例模式.
     *
     * @var string
     */
    public string $instanceType = self::INSTANCE_TYPE_SINGLETON;

    public function __construct(?array $__data = null, ?string $name = null, string $instanceType = self::INSTANCE_TYPE_SINGLETON)
    {
        parent::__construct(...\func_get_args());
    }
}
