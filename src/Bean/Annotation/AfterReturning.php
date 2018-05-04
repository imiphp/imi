<?php
namespace Imi\Bean\Annotation;

/**
 * 在After之后、return之前触发
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Bean\Parser\AopParser")
 */
class AfterReturning extends Base
{
	
}