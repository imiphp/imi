<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\AfterReturning;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\Inject;
use Imi\Aop\AopManager;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Aop\Model\AopItem;
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
     * 类注入缓存.
     *
     * @var array
     */
    private static array $classInjectsCache = [];

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
            self::doAspect($className, $method, 'around', function (AopItem $aopItem, Around $annotation) use (&$aroundAspectDoList) {
                $aroundAspectDoList[] = $aopItem->getCallback();
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
                $joinPoint = new AroundJoinPoint('around', $method, $args, $object, (null === $nextJoinPoint ? function (?array $inArgs = null) use ($object, $className, $method, &$args, $callback) {
                    if (null !== $inArgs)
                    {
                        $args = $inArgs;
                    }

                    return self::callOrigin($object, $className, $method, $args, $callback);
                } : function (?array $inArgs = null) use ($nextAroundAspectDo, $nextJoinPoint, &$args) {
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
            self::doAspect($className, $method, 'afterThrowing', function (AopItem $aopItem, AfterThrowing $annotation) use ($object, $method, &$args, $throwable, &$isCancelThrow) {
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
                ($aopItem->getCallback())($joinPoint);
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
    }

    /**
     * 注入属性.
     *
     * @param object $object
     * @param string $className
     * @param bool   $reInit
     *
     * @return void
     */
    public static function injectProps(object $object, string $className, bool $reInit = false)
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
                if ($reInit && $annotation instanceof Inject)
                {
                    continue;
                }
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
        $beans = Config::get('@currentServer.beans');
        if (isset($beans[$beanName]))
        {
            return $beans[$beanName];
        }
        elseif ($beanName !== $className)
        {
            return $beans[$className] ?? [];
        }

        return [];
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
        if (isset(self::$classInjectsCache[$className]))
        {
            $injects = self::$classInjectsCache[$className];
        }
        else
        {
            $injects = self::$classInjectsCache[$className] = AnnotationManager::getPropertiesAnnotations($className, BaseInjectValue::class);
        }
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
        self::doAspect($className, $method, 'before', function (AopItem $aopItem, Before $annotation) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('before', $method, $args, $object);
            ($aopItem->getCallback())($joinPoint);
        });
        // 原始方法调用
        $result = $callback(...$args);
        // after
        self::doAspect($className, $method, 'after', function (AopItem $aopItem, After $annotation) use ($object, $method, &$args) {
            $joinPoint = new JoinPoint('after', $method, $args, $object);
            ($aopItem->getCallback())($joinPoint);
        });
        // afterReturning
        self::doAspect($className, $method, 'afterReturning', function (AopItem $aopItem, AfterReturning $annotation) use ($object, $method, &$args, &$result) {
            $joinPoint = new AfterReturningJoinPoint('afterReturning', $method, $args, $object);
            $joinPoint->setReturnValue($result);
            ($aopItem->getCallback())($joinPoint);
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
        switch ($pointType)
        {
            case 'before':
                $items = AopManager::getBeforeItems($className, $method);
                break;
            case 'after':
                $items = AopManager::getAfterItems($className, $method);
                break;
            case 'around':
                $items = AopManager::getAroundItems($className, $method);
                break;
            case 'afterReturning':
                $items = AopManager::getAfterReturningItems($className, $method);
                break;
            case 'afterThrowing':
                $items = AopManager::getAfterThrowingItems($className, $method);
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown pointType %s', $pointType));
        }
        $class = 'Imi\Aop\Annotation\\' . Text::toPascalName($pointType);
        foreach ($items as $item)
        {
            $point = new $class($item->getOptions()['extra']);
            $callback($item, $point);
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
