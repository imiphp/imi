<?php

declare(strict_types=1);

namespace Imi\JWT\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * JWT 验证注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class JWTValidation extends Base
{
    public function __construct(
        /**
         * JWT 配置名称.
         */
        public ?string $name = null,
        /**
         * 验证 ID；为 null 则使用配置中的值验证；为 false 则不验证
         *
         * @var string|false|null
         */
        public $id = null,
        /**
         * 验证发行人；为 null 则使用配置中的值验证；为 false 则不验证
         *
         * @var string|false|null
         */
        public $issuer = null,
        /**
         * 验证接收；为 null 则使用配置中的值验证；为 false 则不验证
         *
         * @var string|false|null
         */
        public $audience = null,
        /**
         * 验证主题；为 null 则使用配置中的值验证；为 false 则不验证
         *
         * @var string|false|null
         */
        public $subject = null,
        /**
         * Token 对象注入的参数名称.
         */
        public ?string $tokenParam = null,
        /**
         * 数据注入的参数名称.
         */
        public ?string $dataParam = null
    ) {
    }
}
