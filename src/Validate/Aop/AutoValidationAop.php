<?php
namespace Imi\Validate\Aop;

use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\BeanFactory;
use Imi\Validate\Validator;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Aspect
 */
class AutoValidationAop
{
    /**
     * 类构造方法-自动验证支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION_CONSTRUCT,
     *         allow={
     *             \Imi\Validate\Annotation\AutoValidation::class
     *         }
     * )
     * @After
     * @return mixed
     */
    public function validateConstruct(JoinPoint $joinPoint)
    {
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());

        $annotations = AnnotationManager::getClassAnnotations($className);
        $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className);

        foreach($propertyAnnotations as $propertyName => $tAnnotations)
        {
            foreach($tAnnotations as $annotation)
            {
                $annotation = clone $annotation;
                $annotation->name = $propertyName;
                $annotations[] = $annotation;
            }
        }

        if(isset($annotations[0]))
        {
            $data = [];
            foreach($joinPoint->getTarget() as $name => $value)
            {
                $data[$name] = $value;
            }

            $validator = new Validator($data, $annotations);
            if(!$validator->validate())
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
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Validate\Annotation\AutoValidation::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function validateMethod(AroundJoinPoint $joinPoint)
    {
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $methodName = $joinPoint->getMethod();

        $annotations = AnnotationManager::getMethodAnnotations($className, $methodName);
        if(isset($annotations[0]))
        {
            $methodRef = new \ReflectionMethod($className, $methodName);
            $args = $joinPoint->getArgs();
            $argCount = count($args);
            $paramNames = [];
            foreach($methodRef->getParameters() as $i => $param)
            {
                $paramNames[] = $param->name;
                if($i >= $argCount - 1)
                {
                    break;
                }
            }
            $data = array_combine($paramNames, $args);

            $validator = new Validator($data, $annotations);
            if(!$validator->validate())
            {
                $rule = $validator->getFailRule();
                $exception = $rule->exception;
                throw new $exception(sprintf('%s:%s() Parameter verification is incorrect: %s', $className, $methodName, $validator->getMessage()), $rule->exCode);
            }
        }
        else
        {
            $data = null;
        }

        return $joinPoint->proceed($data);
    }

}