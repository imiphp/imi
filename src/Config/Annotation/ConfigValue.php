<?php

namespace Imi\Config\Annotation;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\Annotation\Parser;
use Imi\Config;

/**
 * 从配置中读取值
 *
 * 支持在注解中为属性动态赋值
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Config\Annotation\Parser\ConfigParser")
 */
class ConfigValue extends BaseInjectValue
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 配置名，支持@app、@currentServer等用法.
     *
     * @var string
     */
    public $name;

    /**
     * 配置不存在时，返回的默认值
     *
     * @var mixed
     */
    public $default;

    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return Config::get($this->name, $this->default);
    }
}
