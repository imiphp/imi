<?php
namespace Imi\Bean\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;

/**
 * 回调注解，返回该回调的返回值
 * @Annotation
 * @Target("ANNOTATION")
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class CallableValue extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'callable';

    /**
     * 回调
     * @var string
     */
    protected $callable;

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return ($this->callable)();
    }
}