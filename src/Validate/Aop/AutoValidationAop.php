<?php

declare(strict_types=1);

namespace Imi\Validate\Aop;

use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Validate\Validator;

/**
 * @Aspect
 */
class AutoValidationAop
{
    /**
     * 类构造方法-自动验证支持
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION_CONSTRUCT,
     *         allow={
     *             \Imi\Validate\Annotation\AutoValidation::class
     *         }
     * )
     * @After
     *
     * @return mixed
     */
    public function validateConstruct(JoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $className = BeanFactory::getObjectClass($target);

        $annotations = AnnotationManager::getClassAnnotations($className);
        $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className);

        foreach ($propertyAnnotations as $propertyName => $tAnnotations)
        {
            foreach ($tAnnotations as $annotation)
            {
                $annotation = clone $annotation;
                $annotation->name = $propertyName;
                $annotations[] = $annotation;
            }
        }

        if ($annotations)
        {
            $data = [];
            foreach ($target as $name => $value)
            {
                $data[$name] = $value;
            }

            $validator = new Validator($data, $annotations);
            if (!$validator->validate())
            {
                $rule = $validator->getFailRule();
                $exception = $rule->exception;
                throw new $exception(sprintf('%s:__construct() Parameter verification is incorrect: %s', $className, $validator->getMessage()), $rule->exCode);
            }
        }
        else
        {
            $data = null;
        }
    }

    /**
     * 方法调用-自动验证支持
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Validate\Annotation\AutoValidation::class
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function validateMethod(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $className = BeanFactory::getObjectClass($target);
        $methodName = $joinPoint->getMethod();

        $annotations = AnnotationManager::getMethodAnnotations($className, $methodName);
        if ($annotations)
        {
            $data = ClassObject::convertArgsToKV($className, $methodName, $joinPoint->getArgs());
            $data['$this'] = $target;

            $validator = new Validator($data, $annotations);
            if (!$validator->validate())
            {
                $rule = $validator->getFailRule();
                $exception = $rule->exception;
                throw new $exception($validator->getMessage(), $rule->exCode);
            }

            unset($data['$this']);
            $data = array_values($data);
        }
        else
        {
            $data = null;
        }

        return $joinPoint->proceed($data);
    }
}
