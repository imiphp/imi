<?php
namespace Imi\HttpValidate\Aop;

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
use Imi\HttpValidate\Annotation\ExtractData;
use Imi\Util\ObjectArrayHelper;

/**
 * @Aspect
 */
class AutoValidationAop
{
    /**
     * 验证 Http 参数
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\HttpValidate\Annotation\HttpValidation::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function validateHttp(AroundJoinPoint $joinPoint)
    {
        $controller = $joinPoint->getTarget();
        $className = BeanFactory::getObjectClass($controller);
        $methodName = $joinPoint->getMethod();

        $annotations = AnnotationManager::getMethodAnnotations($className, $methodName);
        if(isset($annotations[0]))
        {
            $methodRef = new \ReflectionMethod($className, $methodName);
            $args = $joinPoint->getArgs();
            $argCount = count($args);
            $paramNames = [];
            $paramNameRelation = [];
            foreach($methodRef->getParameters() as $i => $param)
            {
                if($i < $argCount)
                {
                    $paramNames[] = $param->name;
                }
                $paramNameRelation[$param->name] = $i;
            }
            $params = array_combine($paramNames, $args);

            $data['$get'] = $controller->request->get();
            $data['$post'] = $controller->request->post();
            $data['$body'] = $controller->request->getParsedBody();

            $validator = new Validator($data, $annotations);
            if(!$validator->validate())
            {
                throw new \InvalidArgumentException($validator->getMessage());
            }

            foreach($annotations as $annotation)
            {
                if($annotation instanceof ExtractData)
                {
                    list($key, $name) = explode('.', $annotation->name, 2);
                    if(isset($paramNameRelation[$annotation->to]))
                    {
                        $data[$paramNameRelation[$annotation->to]] = ObjectArrayHelper::get($data[$key], $name);
                    }
                }
            }

            unset($data['$get'], $data['$post'], $data['$body']);

        }
        else
        {
            $data = null;
        }

        return $joinPoint->proceed($data);
    }

}