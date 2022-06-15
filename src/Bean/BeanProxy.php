<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Aop\AfterReturningJoinPoint;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\AfterReturning;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\Inject;
use Imi\Aop\AopManager;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\JoinPoint;
use Imi\Aop\Model\AopItem;
use Imi\Config;
use Imi\Util\Imi;

class BeanProxy
{
    /**
     * 魔术方法.
     *
     * @return mixed
     */
    public static function call(object $object, string $className, string $method, callable $callback, array &$args)
    {
        try
        {
            // 先尝试环绕
            $aroundAspectDoList = [];
            self::doAspect($className, $method, 'Around', static function (AopItem $aopItem, Around $annotation) use (&$aroundAspectDoList) {
                $aroundAspectDoList[] = $aopItem->getCallback();
            });
            if (!$aroundAspectDoList)
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
                } : static function (?array $inArgs = null) use ($nextAroundAspectDo, $nextJoinPoint, &$args) {
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
            self::doAspect($className, $method, 'AfterThrowing', static function (AopItem $aopItem, AfterThrowing $annotation) use ($object, $method, &$args, $throwable, &$isCancelThrow) {
                // 验证异常是否捕获
                if ($annotation->allow || $annotation->deny)
                {
                    $throwableClassName = \get_class($throwable);
                    if ($annotation->allow)
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
                    if ($annotation->deny)
                    {
                        foreach ($annotation->deny as $rule)
                        {
                            $denyResult = Imi::checkRuleMatch($rule, $throwableClassName);
                            if ($denyResult)
                            {
                                return;
                            }
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
     * 注入属性.
     */
    public static function injectProps(object $object, string $className, bool $reInit = false): void
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
            foreach ($injects as $propName => $annotationOption)
            {
                $class = $annotationOption['injectType'];
                if ($reInit && (Inject::class === $class || is_subclass_of($class, Inject::class)))
                {
                    continue;
                }
                /** @var \Imi\Aop\Annotation\BaseInjectValue $annotation */
                $annotation = new $class($annotationOption['injectOptions']);
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
                $propRef->setAccessible(true);
                $propRef->setValue($object, $value);
            }
        }
    }

    /**
     * 获取注入属性的配置们.
     */
    public static function getConfigInjects(string $className): array
    {
        // 配置文件注入
        $beanData = BeanManager::get($className);
        if ($beanData)
        {
            $beanName = $beanData['beanName'];
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
        elseif ($beanName !== $className && isset($beans[$className]))
        {
            return $beans[$className];
        }
        else
        {
            $beans = Config::get('@app.beans');
            if (isset($beans[$beanName]))
            {
                return $beans[$beanName];
            }
            elseif ($beanName !== $className && isset($beans[$className]))
            {
                return $beans[$className];
            }
        }

        return [];
    }

    /**
     * 获取注入类属性的注解和配置.
     *
     * 返回：[$annotations, $configs]
     */
    public static function getInjects(string $className): array
    {
        $configs = static::getConfigInjects($className);
        $injects = BeanManager::getPropertyInjects($className);
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
     * @return mixed
     */
    private static function callOrigin(object $object, string $className, string $method, array &$args, callable $callback)
    {
        // before
        self::doAspect($className, $method, 'Before', static function (AopItem $aopItem, Before $annotation) use ($object, $method, &$args) {
            ($aopItem->getCallback())(new JoinPoint('before', $method, $args, $object));
        });
        // 原始方法调用
        $result = $callback(...$args);
        // after
        self::doAspect($className, $method, 'After', static function (AopItem $aopItem, After $annotation) use ($object, $method, &$args) {
            ($aopItem->getCallback())(new JoinPoint('after', $method, $args, $object));
        });
        // afterReturning
        self::doAspect($className, $method, 'AfterReturning', static function (AopItem $aopItem, AfterReturning $annotation) use ($object, $method, &$args, &$result) {
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
     * @param string   $method    方法名
     * @param string   $pointType 切入点类型
     * @param callable $callback  回调
     */
    private static function doAspect(string $className, string $method, string $pointType, callable $callback): void
    {
        switch ($pointType)
        {
            case 'Before':
                $items = AopManager::getBeforeItems($className, $method);
                break;
            case 'After':
                $items = AopManager::getAfterItems($className, $method);
                break;
            case 'Around':
                $items = AopManager::getAroundItems($className, $method);
                break;
            case 'AfterReturning':
                $items = AopManager::getAfterReturningItems($className, $method);
                break;
            case 'AfterThrowing':
                $items = AopManager::getAfterThrowingItems($className, $method);
                break;
            default:
                throw new \RuntimeException(sprintf('Unknown pointType %s', $pointType));
        }
        if ($items)
        {
            $class = 'Imi\Aop\Annotation\\' . $pointType;
            foreach ($items as $item)
            {
                $callback($item, new $class($item->getOptions()['extra'] ?? []));
            }
        }
    }

    /**
     * 获取注入类属性的值
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
            if (isset($annotations[$propertyName]))
            {
                return (new $annotations[$propertyName]['injectType']($annotations[$propertyName]['injectOptions']))->getRealValue();
            }
            else
            {
                return null;
            }
        }
    }
}
