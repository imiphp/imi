<?php

namespace Imi\JWT\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * JWT 验证注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class JWTValidation extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * JWT 配置名称.
     *
     * @var string
     */
    public $name;

    /**
     * 验证 ID
     * 为 null 则使用配置中的值验证
     * 为 false 则不验证
     *
     * @var string|false|null
     */
    public $id;

    /**
     * 验证发行人
     * 为 null 则使用配置中的值验证
     * 为 false 则不验证
     *
     * @var string|false|null
     */
    public $issuer;

    /**
     * 验证接收
     * 为 null 则使用配置中的值验证
     * 为 false 则不验证
     *
     * @var string|false|null
     */
    public $audience;

    /**
     * 验证主题
     * 为 null 则使用配置中的值验证
     * 为 false 则不验证
     *
     * @var string|false|null
     */
    public $subject;

    /**
     * Token 对象注入的参数名称.
     *
     * @var string
     */
    public $tokenParam;

    /**
     * 数据注入的参数名称.
     *
     * @var string
     */
    public $dataParam;
}
