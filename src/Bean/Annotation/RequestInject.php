<?php
namespace Imi\Bean\Annotation;

/**
 * 属性注入
 * 使用：RequestContext::getBean()
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class RequestInject extends Inject
{
	
}