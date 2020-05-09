<?php
namespace Imi\HttpValidate\Aop;

use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Validate\Validator;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Server\Session\Session;
use Imi\Util\ObjectArrayHelper;
use Imi\Aop\Annotation\PointCut;
use Imi\Bean\Annotation\AnnotationManager;

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
     * @Before
     * @return mixed
     */
    public function validateHttp(JoinPoint $joinPoint)
    {
        $controller = $joinPoint->getTarget();
        $className = BeanFactory::getObjectClass($controller);
        $methodName = $joinPoint->getMethod();

        $annotations = AnnotationManager::getMethodAnnotations($className, $methodName);
        if(isset($annotations[0]))
        {
            $data = ClassObject::convertArgsToKV($className, $methodName, $joinPoint->getArgs());

            $controllerRequest = $controller->request;
            $data['$get'] = $controllerRequest->get();
            $data['$post'] = $controllerRequest->post();
            $data['$body'] = $controllerRequest->getParsedBody();
            $data['$headers'] = [];
            foreach ($controllerRequest->getHeaders() as $name => $values)
            {
                $data['$headers'][$name] = implode(', ', $values);
            }
            $data['$cookie'] = $controllerRequest->getCookieParams();
            $data['$session'] = Session::get();
            $data['$this'] = $controller;

            $validator = new Validator($data, $annotations);
            if(!$validator->validate())
            {
                $rule = $validator->getFailRule();
                $exception = $rule->exception;
                throw new $exception($validator->getMessage(), $rule->exCode);
            }

            unset($data['$get'], $data['$post'], $data['$body'], $data['$headers'], $data['$cookie'], $data['$session'], $data['$this']);

            $data = array_values($data);
        }
        else
        {
            $data = null;
        }

        $joinPoint->setArgs($data);
    }

}