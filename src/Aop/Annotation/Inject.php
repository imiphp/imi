<?php

namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\Bean\Annotation\Parser;

/**
 * 对象注入
 * 使用：App::getBean().
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class Inject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * Bean名称或类名.
     */
    public $name;

    /**
     * Bean实例化参数.
     *
     * @var array
     */
    public $args = [];

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return App::getBean($this->name, ...$this->args);
    }
}
