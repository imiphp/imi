<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * Bean.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[\Imi\Bean\Annotation\Parser(className: \Imi\Bean\Parser\BeanParser::class)]
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

    public function __construct(
        /**
         * Bean名称，留空则为当前类名（包含完整命名空间）.
         */
        public ?string $name = null,
        /**
         * 实例化类型，默认为单例模式.
         */
        public string $instanceType = self::INSTANCE_TYPE_SINGLETON,
        /**
         * 是否启用递归特性.
         */
        public bool $recursion = false,
        /**
         * 限制生效的环境，为 null 时则不限制.
         *
         * @var string|array|null
         */
        public $env = null
    ) {
    }
}
