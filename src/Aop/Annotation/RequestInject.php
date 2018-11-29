<?php
namespace Imi\Aop\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 属性注入
 * 使用：RequestContext::getBean()
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class RequestInject extends Inject
{
    
}