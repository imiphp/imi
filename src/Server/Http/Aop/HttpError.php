<?php
namespace Imi\Server\Http\Aop;

use Imi\Util\Format\Json;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Util\Http\Consts\MediaType;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\App;

/**
 * @Aspect
 */
class HttpError
{
    /**
     * 异常捕获
     * @PointCut(
     *         allow={
     *             "Imi\Server\Http\Listener\BeforeRequest::handle",
     *         }
     * )
     * @AfterThrowing
     * @param AfterThrowingJoinPoint $joinPoint
     * @return void
     */
    public function afterThrowing(AfterThrowingJoinPoint $joinPoint)
    {
		if(true === App::getBean('HttpErrorHandler')->handle($joinPoint->getThrowable()))
		{
			$joinPoint->cancelThrow();
		}
    }
}