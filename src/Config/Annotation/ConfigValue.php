<?php
namespace Imi\Config\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 从配置中读取值
 * 
 * 支持在注解中为属性动态赋值
 * 
 * @Annotation
 * @Target("ANNOTATION")
 * @Parser("Imi\Config\Annotation\Parser\ConfigParser")
 */
class ConfigValue extends Base
{
    /**
     * 只传一个参数时的参数名
     * @var string
     */
    protected $defaultFieldName = 'name';

    /**
     * 配置名，支持@app、@currentServer等用法
     *
     * @var string
     */
    public $name;

}
