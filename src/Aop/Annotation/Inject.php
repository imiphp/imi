<?php
namespace Imi\Aop\Annotation;

use Imi\App;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation\Parser;

/**
 * 属性注入
 * 使用：App::getBean()
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class Inject extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * Bean名称或类名
     */
    public $name;

    /**
     * Bean实例化参数
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
        if($this instanceof RequestInject && Coroutine::isIn())
        {
            return RequestContext::getBean($this->name, ...$this->args);
        }
        else if($this instanceof Inject)
        {
            return App::getBean($this->name, ...$this->args);
        }
        else
        {
            return null;
        }
    }
}