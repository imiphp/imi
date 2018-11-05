<?php
namespace Imi\Validate\Annotation;

use Imi\Config;
use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

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
     * 属性注解可省略
     *
     * @var string
     */
    public $name;

    /**
     * 当值不符合条件时的默认值
     *
     * @var mixed
     */
    public $default;

    /**
     * 对结果取反
     *
     * @var boolean
     */
    public $inverseResult = false;

    /**
     * 当验证条件不符合时的信息
     * 
     * 支持代入{:value}原始值
     * 支持以{name}这样的形式，代入注解参数值
     *
     * @var string
     */
    public $message = '{name} validate failed';

    /**
     * 验证回调
     *
     * @var callable
     */
    public $callable;

    /**
     * 参数名数组
     *
     * @var array
     */
    public $args;

    /**
     * 异常类
     *
     * @var string
     */
    public $exception = null;

    /**
     * 异常编码
     *
     * @var integer
     */
    public $exCode = 0;

    public function __construct($data = [])
    {
        parent::__construct($data);
        if(null === $this->exception)
        {
            $this->exception = Config::get('@app.validation.exception', \InvalidArgumentException::class);
        }
        if(null === $this->exCode)
        {
            $this->exCode = Config::get('@app.validation.exCode', 0);
        }
    }
}