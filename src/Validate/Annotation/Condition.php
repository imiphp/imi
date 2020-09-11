<?php

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Config;

/**
 * 通用验证条件
 * 传入回调进行验证
 *
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 * @Parser("\Imi\Validate\Annotation\Parser\ValidateConditionParser")
 */
class Condition extends Base
{
    /**
     * 参数名称
     * 属性注解可省略.
     *
     * @var string
     */
    public $name;

    /**
     * 非必验证，只有当值存在才验证
     *
     * @var bool
     */
    public $optional = false;

    /**
     * 当值不符合条件时的默认值
     *
     * @var mixed
     */
    public $default;

    /**
     * 对结果取反.
     *
     * @var bool
     */
    public $inverseResult = false;

    /**
     * 当验证条件不符合时的信息.
     *
     * 支持代入{:value}原始值
     * 支持代入{:data.xxx}所有数据中的某项
     * 支持以{name}这样的形式，代入注解参数值
     *
     * @var string
     */
    public $message = '{name} validate failed';

    /**
     * 验证回调.
     *
     * @var callable
     */
    public $callable;

    /**
     * 参数名数组.
     *
     * 支持代入{:value}原始值
     * 支持代入{:data}所有数据
     * 支持代入{:data.xxx}所有数据中的某项
     * 支持以{name}这样的形式，代入注解参数值
     * 如果没有{}，则原样传值
     *
     * @var array
     */
    public $args = ['{:value}'];

    /**
     * 异常类.
     *
     * @var string
     */
    public $exception = null;

    /**
     * 异常编码
     *
     * @var int
     */
    public $exCode = null;

    public function __construct($data = [])
    {
        parent::__construct($data);
        if (null === $this->exception)
        {
            $this->exception = Config::get('@app.validation.exception', \InvalidArgumentException::class);
        }
        if (null === $this->exCode)
        {
            $this->exCode = Config::get('@app.validation.exCode', 0);
        }
    }
}
