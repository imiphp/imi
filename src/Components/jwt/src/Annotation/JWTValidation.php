<?php

declare(strict_types=1);

namespace Imi\JWT\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * JWT 验证注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null       $name       JWT 配置名称
 * @property string|false|null $id         验证 ID；为 null 则使用配置中的值验证；为 false 则不验证
 * @property string|false|null $issuer     验证发行人；为 null 则使用配置中的值验证；为 false 则不验证
 * @property string|false|null $audience   验证接收；为 null 则使用配置中的值验证；为 false 则不验证
 * @property string|false|null $subject    验证主题；为 null 则使用配置中的值验证；为 false 则不验证
 * @property string|null       $tokenParam Token 对象注入的参数名称
 * @property string|null       $dataParam  数据注入的参数名称
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class JWTValidation extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * @param string|false|null $id
     * @param string|false|null $issuer
     * @param string|false|null $audience
     * @param string|false|null $subject
     */
    public function __construct(?array $__data = null, ?string $name = null, $id = null, $issuer = null, $audience = null, $subject = null, ?string $tokenParam = null, ?string $dataParam = null)
    {
        parent::__construct(...\func_get_args());
    }
}
