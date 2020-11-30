<?php

declare(strict_types=1);

namespace Imi\Server\Http\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Server\Http\Annotation\ExtractData;
use Imi\Server\Session\Session;
use Imi\Util\ClassObject;
use Imi\Util\ObjectArrayHelper;

/**
 * @Aspect
 */
class ExtractDataAop
{
    /**
     * 处理 ExtractData 注解.
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Server\Http\Annotation\ExtractData::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parseExtractData(AroundJoinPoint $joinPoint)
    {
        $controller = $joinPoint->getTarget();
        $className = BeanFactory::getObjectClass($controller);
        $methodName = $joinPoint->getMethod();

        $annotations = AnnotationManager::getMethodAnnotations($className, $methodName, ExtractData::class);
        if (isset($annotations[0]))
        {
            $controllerRequest = $controller->request;
            $data = ClassObject::convertArgsToKV($className, $methodName, $joinPoint->getArgs());
            $allData = [
                '$get'      => $controllerRequest->get(),
                '$post'     => $controllerRequest->post(),
                '$body'     => $controllerRequest->getParsedBody(),
                '$headers'  => [],
                '$cookie'   => $controllerRequest->getCookieParams(),
                '$session'  => Session::get(),
                '$this'     => $controller,
            ];
            $headers = &$allData['$headers'];
            foreach ($controllerRequest->getHeaders() as $name => $values)
            {
                $headers[$name] = implode(', ', $values);
            }

            foreach ($annotations as $annotation)
            {
                $data[$annotation->to] = ObjectArrayHelper::get($allData, $annotation->name, $annotation->default);
            }

            $data = array_values($data);
        }
        else
        {
            $data = null;
        }

        return $joinPoint->proceed($data);
    }
}
