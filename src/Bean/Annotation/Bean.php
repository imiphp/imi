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
     * @var string
     */
    public $name;

    /**
     * 实例化类型，默认为单例模式.
     *
     * @var string
     */
    public $instanceType;

    public function __construct($data)
    {
        parent::__construct($data);
        if (null === $this->instanceType)
        {
            $this->instanceType = static::INSTANCE_TYPE_SINGLETON;
        }
    }
}
