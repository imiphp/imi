<?php
namespace Imi\Validate\Aop;

use Imi\Aop\PointCutType;
use Imi\Bean\BeanFactory;
use Imi\Validate\Validator;
use Imi\Aop\AroundJoinPoint;
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
     * 自动事务支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Validate\Annotation\AutoValidation::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function validate(AroundJoinPoint $joinPoint)
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
                throw new \InvalidArgumentException(sprintf('%s:%s() Parameter verification is incorrect: %s', $className, $methodName, $validator->getMessage()));
            }
        }

        $joinPoint->proceed($data);
    }

}