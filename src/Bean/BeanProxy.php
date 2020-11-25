<?php

namespace Imi\Bean;

use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Parser\BeanParser;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Util\Text;

class BeanProxy
{
    /**
     * 存储每个类对应的切面关系.
     *
     * @var \SplPriorityQueue[]
     */
    private static array $aspects = [];

    /**
     * 切面缓存.
     *
     * @var array
     */
    private static array $aspectCache = [];

    /**
     * 魔术方法.
     *
     * @param object   $object
     * @param string   $className
     * @param string   $method
     * @param callable $callback
     * @param array    $args
     *
     * @return mixed
     */
    public static function call(object $object, string $className, string $method, callable $callback, array &$args)
    {
        try
        {
            // 先尝试环绕
            $aroundAspectDoList = [];
            self::doAspect($className, $method, 'around', function ($aspectClassName, $methodName) use (&$aroundAspectDoList) {
                $aroundAspectDoList[] = [new $aspectClassName(), $methodName];
            });
            if (!isset($aroundAspectDoList[0]))
            {
                // 正常请求
                return self::callOrigin($object, $className, $method, $args, $callback);
            }
            $aroundAspectDoList = array_reverse($aroundAspectDoList);

            $nextJoinPoint = null;
            $nextAroundAspectDo = null;

            foreach ($aroundAspectDoList as $aroundAspectDo)
            {
                $joinPoint = new AroundJoinPoint('around', $method, $args, $object, (null === $nextJoinPoint ? function ($inArgs = null) use ($object, $className, $method, &$args, $callback) {
                    if (null !== $inArgs)
                    {
                        $args = $inArgs;
                    }

                    return self::callOrigin($object, $className, $method, $args, $callback);
                } : function ($inArgs = null) use ($nextAroundAspectDo, $nextJoinPoint, &$args) {
                    if (null !== $inArgs)
                    {
                        $args = $inArgs;
                    }

                    return $nextAroundAspectDo($nextJoinPoint);
                }));
                $nextJoinPoint = $joinPoint;
                $nextAroundAspectDo = $aroundAspectDo;
            }

            return $nextAroundAspectDo($nextJoinPoint);
        }
        catch (\Throwable $throwable)
        {
            // 异常
            $isCancelThrow = false;
            self::doAspect($className, $method, 'afterThrowing', function ($aspectClassName, $methodName, AfterThrowing $annotation) use ($object, $method, &$args, $throwable, &$isCancelThrow) {
                // 验证异常是否捕获
                if (isset($annotation->allow[0]) || isset($annotation->deny[0]))
                {
                    $throwableClassName = \get_class($throwable);
                    if (isset($annotation->allow[0]))
                    {
                        $allowResult = false;
                        foreach ($annotation->allow as $rule)
                        {
                            $allowResult = Imi::checkRuleMatch($rule, $throwableClassName);
                            if ($allowResult)
                            {
                                break;
                            }
                        }
                        if (!$allowResult)
                        {
                            return;
                        }
                    }
                    $denyResult = false;
                    foreach ($annotation->deny as $rule)
                    {
                        $denyResult = Imi::checkRuleMatch($rule, $throwableClassName);
                        if ($denyResult)
                        {
                            return;
                        }
                    }
                }
                // 处理
                $joinPoint = new AfterThrowingJoinPoint('afterThrowing', $method, $args, $object, $throwable);
                $aspectObject = new $aspectClassName();
                $aspectObject->$methodName($joinPoint);
                if (!$isCancelThrow && $joinPoint->isCancelThrow())
                {
                    $isCancelThrow = true;
                }
            });
            // 不取消依旧抛出
            if (!$isCancelThrow)
            {
                throw $throwable;
            }
        }
    }

    /**
     * 初始化.
     *
     * @param string $className
     *
     * @return void
     */
    public static function init(string $className)
    {
        // 每个类只需处理一次
        $staticAspects = &static::$aspects;
        if (isset($staticAspects[$className]))
        {
            return;
        }
        $aspects = AnnotationManager::getAnnotationPoints(Aspect::class);
        if (!$aspects)
        {
            return;
        }
        $refClass = ReflectionContainer::getClassReflection($className);
        $staticAspects[$className] = $aspect = new \SplPriorityQueue();
        foreach ($aspects as $item)
        {
            $itemClass = $item->getClass();
            $itemPriority = $item->getAnnotation()->priority;
            // 判断是否属于当前类方法的切面
            $pointCutsSet = AnnotationManager::getMethodsAnnotations($itemClass, PointCut::class);
            if (!$pointCutsSet)
            {
                continue;
            }
            foreach ($pointCutsSet as $methodName => $pointCuts)
            {
                foreach ($pointCuts as $pointCut)
                {
                    switch ($pointCut->type)
                        {
                            case PointCutType::METHOD:
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkClassRule($allowItem, $className))
                                    {
                                        $aspect->insert([
                                            'class'     => $itemClass,
                                            'method'    => $methodName,
                                            'pointCut'  => $pointCut,
                                        ], $itemPriority);
                                        break;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION:
                                foreach ($refClass->getMethods() as $method)
                                {
                                    $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method->getName());
                                    foreach ($pointCut->allow as $allowItem)
                                    {
                                        foreach ($methodAnnotations as $annotation)
                                        {
                                            if ($annotation instanceof $allowItem)
                                            {
                                                $aspect->insert([
                                                    'class'     => $itemClass,
                                                    'method'    => $methodName,
                                                    'pointCut'  => $pointCut,
                                                ], $itemPriority);
                                                break 3;
                                            }
                                        }
                                    }
                                }
                                break;
                            case PointCutType::CONSTRUCT:
                                // 构造方法
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkRuleMatch($allowItem, $className))
                                    {
                                        $aspect->insert([
                                            'class'     => $itemClass,
                                            'method'    => $methodName,
                                            'pointCut'  => $pointCut,
                                        ], $itemPriority);
                                        break;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION_CONSTRUCT:
                                // 注解构造方法
                                if (!isset($classAnnotations))
                                {
                                    $classAnnotations = AnnotationManager::getClassAnnotations($className);
                                }
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    foreach ($classAnnotations as $annotation)
                                    {
                                        if ($annotation instanceof $allowItem)
                                        {
                                            $aspect->insert([
                                                'class'     => $itemClass,
                                                'method'    => $methodName,
                                                'pointCut'  => $pointCut,
                                            ], $itemPriority);
                                            break 2;
                                        }
                                    }
                                }
                                break;
                        }
                }
            }
        }
    }

    /**
     * 注入属性.
     *
     * @param object $object
     * @param string $className
     *
     * @return void
     */
    public static function injectProps(object $object, string $className)
    {
        [$injects, $configs] = static::getInjects($className);
        if (!$injects && !$configs)
        {
            return;
        }
        $refClass = ReflectionContainer::getClassReflection($className);

        // @inject()和@requestInject()注入
        if ($injects)
        {
            foreach ($injects as $propName => $annotations)
            {
                $annotation = reset($annotations);
                $propRef = $refClass->getProperty($propName);
                $propRef->setAccessible(true);
                $propRef->setValue($object, $annotation->getRealValue());
            }
        }

        // 配置注入
        if ($configs)
        {
            foreach ($configs as $name => $value)
            {
                $propRef = $refClass->getProperty($name);
                if (null === $propRef)
                {
                    continue;
                }
                $propRef->setAccessible(true);
                $propRef->setValue($object, $value);
            }
        }
    }

    /**
     * 获取注入属性的配置们.
     *
     * @param string $className
     *
     * @return array
     */
    public static function getConfigInjects(string $className): array
    {
        // 配置文件注入
        $beanData = BeanParser::getInstance()->getData();
        if (isset($beanData[$className]))
        {
            $beanName = $beanData[$className]['beanName'];
        }
        else
        {
            $beanName = $className;
        }
        $beanProperties = Config::get('@currentServer.beans.' . $beanName);
        if (null === $beanProperties && $beanName !== $className)
        {
            $beanProperties = Config::get('@currentServer.beans.' . $className);
        }

        return $beanProperties ?? [];
    }

    /**
     * 获取注入类属性的注解和配置.
     *
     * 返回：[$annotations, $configs]
     *
     * @param string $className
     *
     * @return array
     */
    public static function getInjects(string $className): array
    {
        $injects = AnnotationManager::getPropertiesAnnotations($className, BaseInjectValue::class);
        $configs = static::getConfigInjects($className);
        if ($configs && $injects)
        {
            foreach ($configs as $key => $value)
            {
                if (isset($injects[$key]))
                {
                    unset($injects[$key]);
                }
            }
        }

        return [$injects, $configs];
    }

    /**
     * 正常请求
     *
     * @param object $object
     * @param string $className
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    private static function callOrigin(object $object, string $className, string $method, array &$args, callable $callback)
    {
        // before
        self::doAspect($className, $method, 'before', function ($aspectClassName, $methodName) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('before', $method, $args, $object);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
        });
        // 原始方法调用
        $result = $callback(...$args);
        // after
        self::doAspect($className, $method, 'after', function ($aspectClassName, $methodName) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('after', $method, $args, $object);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
        });
        // afterReturning
        self::doAspect($className, $method, 'afterReturning', function ($aspectClassName, $methodName) use ($object, $method, &$args, &$result) {
            $joinPoint = new AfterReturningJoinPoint('afterReturning', $method, $args, $object);
            $joinPoint->setReturnValue($result);
            $aspectObject = new $aspectClassName();
            $aspectObject->$methodName($joinPoint);
            $result = $joinPoint->getReturnValue();
        });

        return $result;
    }

    /**
     * 执行切面操作.
     *
     * @param string   $className
     * @param string   $method    方法名
     * @param string   $pointType 切入点类型
     * @param callable $callback  回调
     *
     * @return void
     */
    private static function doAspect(string $className, string $method, string $pointType, callable $callback)
    {
        $aspectCache = &static::$aspectCache;
        if (isset($aspectCache[$className][$method][$pointType]))
        {
            $tmpItem = $aspectCache[$className][$method][$pointType];
        }
        else
        {
            $tmpItem = [];
            $list = clone static::$aspects[$className];
            $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method);
            foreach ($list as $option)
            {
                $aspectClassName = $option['class'];
                $methodName = $option['method'];
                $pointCut = $option['pointCut'];
                $allowResult = false;
                switch ($pointCut->type)
                {
                    case PointCutType::METHOD:
                        foreach ($pointCut->allow as $rule)
                        {
                            $allowResult = Imi::checkClassMethodRule($rule, $className, $method);
                            if ($allowResult)
                            {
                                break;
                            }
                        }
                        break;
                    case PointCutType::ANNOTATION:
                        foreach ($pointCut->allow as $rule)
                        {
                            foreach ($methodAnnotations as $annotation)
                            {
                                $allowResult = $annotation instanceof $rule;
                                if ($allowResult)
                                {
                                    break 2;
                                }
                            }
                        }
                        break;
                    case PointCutType::CONSTRUCT:
                    case PointCutType::ANNOTATION_CONSTRUCT:
                        if ('__construct' === $method)
                        {
                            $allowResult = true;
                        }
                        break;
                }
                if ($allowResult)
                {
                    $denyResult = false;

                    switch ($pointCut->type)
                    {
                        case PointCutType::METHOD:
                            foreach ($pointCut->deny as $rule)
                            {
                                $denyResult = Imi::checkClassMethodRule($rule, $className, $method);
                                if ($denyResult)
                                {
                                    break;
                                }
                            }
                            break;
                        case PointCutType::ANNOTATION:
                            foreach ($pointCut->deny as $rule)
                            {
                                foreach ($methodAnnotations as $annotation)
                                {
                                    $denyResult = $annotation instanceof $rule;
                                    if ($denyResult)
                                    {
                                        break 2;
                                    }
                                }
                            }
                            break;
                    }

                    if ($denyResult)
                    {
                        continue;
                    }
                    $point = AnnotationManager::getMethodAnnotations($aspectClassName, $methodName, 'Imi\Aop\Annotation\\' . Text::toPascalName($pointType))[0] ?? null;
                    if (null !== $point)
                    {
                        $tmpItem[] = [$aspectClassName, $methodName, $point];
                    }
                }
            }
            $aspectCache[$className][$method][$pointType] = $tmpItem;
        }

        if ($tmpItem)
        {
            foreach ($tmpItem as $item)
            {
                $callback(...$item);
            }
        }
    }

    /**
     * 获取注入类属性的值
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return mixed
     */
    public static function getInjectValue(string $className, string $propertyName)
    {
        [$annotations, $configs] = static::getInjects($className);
        if (isset($configs[$propertyName]))
        {
            return $configs[$propertyName];
        }
        else
        {
            $annotation = $annotations[0] ?? null;
            if ($annotation)
            {
                return $annotation->getRealValue();
            }
            else
            {
                return null;
            }
        }
    }
}
