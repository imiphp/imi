<?php
namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 属性注入
 * 使用：App::getBean()
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class Inject extends Base
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
}