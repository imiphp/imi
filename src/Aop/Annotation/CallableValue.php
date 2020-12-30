<?php

declare(strict_types=1);

namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 回调注解，返回该回调的返回值
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
class CallableValue extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string|null
     */
    protected ?string $defaultFieldName = 'callable';

    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

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
