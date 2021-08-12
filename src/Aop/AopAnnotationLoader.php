<?php

declare(strict_types=1);

namespace Imi\Aop;

use Imi\Aop\Annotation\After;
use Imi\Aop\Annotation\AfterReturning;
use Imi\Aop\Annotation\AfterThrowing;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Model\MethodAnnotationRelation;
use Imi\Util\DelayClassCallable;

class AopAnnotationLoader
{
    private static bool $loaded = false;

    private function __construct()
    {
    }

    public static function load(bool $force = true): void
    {
        if (!$force && self::$loaded)
        {
            return;
        }
        foreach (AnnotationManager::getAnnotationPoints(Aspect::class) as $item)
        {
            /** @var Aspect $aspectAnnotation */
            $aspectAnnotation = $item->getAnnotation();
            $className = $item->getClass();
            foreach (AnnotationManager::getMethodsAnnotations($className, PointCut::class) as $methodName => $pointCuts)
            {
                $callback = new DelayClassCallable($className, $methodName);
                /** @var Before|null $beforeAnnotation */
                $beforeAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, Before::class)[0] ?? null;
                /** @var After|null $beforeAnnotation */
                $afterAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, After::class)[0] ?? null;
                /** @var Around|null $beforeAnnotation */
                $aroundAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, Around::class)[0] ?? null;
                /** @var AfterReturning|null $beforeAnnotation */
                $afterReturningAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, AfterReturning::class)[0] ?? null;
                /** @var AfterThrowing|null $beforeAnnotation */
                $afterThrowingAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, AfterThrowing::class)[0] ?? null;
                /** @var PointCut[] $pointCuts */
                foreach ($pointCuts as $pointCut)
                {
                    switch ($pointCut->type)
                    {
                        case PointCutType::CONSTRUCT:
                        case PointCutType::METHOD:
                            foreach ($pointCut->allow as $allowItem)
                            {
                                if (PointCutType::CONSTRUCT === $pointCut->type)
                                {
                                    $class = $allowItem;
                                    $method = '__construct';
                                }
                                else
                                {
                                    [$class, $method] = explode('::', $allowItem);
                                }
                                if ($beforeAnnotation)
                                {
                                    $options = [
                                        'deny'  => $pointCut->deny,
                                        'extra' => $beforeAnnotation->toArray(),
                                    ];
                                    AopManager::addBefore($class, $method, $callback, $aspectAnnotation->priority, $options);
                                }
                                if ($afterAnnotation)
                                {
                                    $options = [
                                        'deny'  => $pointCut->deny,
                                        'extra' => $afterAnnotation->toArray(),
                                    ];
                                    AopManager::addAfter($class, $method, $callback, $aspectAnnotation->priority, $options);
                                }
                                if ($aroundAnnotation)
                                {
                                    $options = [
                                        'deny'  => $pointCut->deny,
                                        'extra' => $aroundAnnotation->toArray(),
                                    ];
                                    AopManager::addAround($class, $method, $callback, $aspectAnnotation->priority, $options);
                                }
                                if ($afterReturningAnnotation)
                                {
                                    $options = [
                                        'deny'  => $pointCut->deny,
                                        'extra' => $afterReturningAnnotation->toArray(),
                                    ];
                                    AopManager::addAfterReturning($class, $method, $callback, $aspectAnnotation->priority, $options);
                                }
                                if ($afterThrowingAnnotation)
                                {
                                    $options = [
                                        'deny'  => $pointCut->deny,
                                        'extra' => $afterThrowingAnnotation->toArray(),
                                    ];
                                    AopManager::addAfterThrowing($class, $method, $callback, $aspectAnnotation->priority, $options);
                                }
                            }
                            break;
                        case PointCutType::ANNOTATION_CONSTRUCT:
                        case PointCutType::ANNOTATION:
                            if (PointCutType::ANNOTATION_CONSTRUCT === $pointCut->type)
                            {
                                $where = 'class';
                            }
                            else
                            {
                                $where = 'method';
                            }
                            foreach ($pointCut->allow as $allowItem)
                            {
                                /** @var MethodAnnotationRelation $point */
                                foreach (AnnotationManager::getAnnotationPoints($allowItem, $where) as $point)
                                {
                                    $class = $point->getClass();
                                    if (PointCutType::ANNOTATION_CONSTRUCT === $pointCut->type)
                                    {
                                        $method = '__construct';
                                    }
                                    else
                                    {
                                        $method = $point->getMethod();
                                    }
                                    if ($beforeAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $beforeAnnotation->toArray(),
                                        ];
                                        AopManager::addBefore($class, $method, $callback, $aspectAnnotation->priority, $options);
                                    }
                                    if ($afterAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterAnnotation->toArray(),
                                        ];
                                        AopManager::addAfter($class, $method, $callback, $aspectAnnotation->priority, $options);
                                    }
                                    if ($aroundAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $aroundAnnotation->toArray(),
                                        ];
                                        AopManager::addAround($class, $method, $callback, $aspectAnnotation->priority, $options);
                                    }
                                    if ($afterReturningAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterReturningAnnotation->toArray(),
                                        ];
                                        AopManager::addAfterReturning($class, $method, $callback, $aspectAnnotation->priority, $options);
                                    }
                                    if ($afterThrowingAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterThrowingAnnotation->toArray(),
                                        ];
                                        AopManager::addAfterThrowing($class, $method, $callback, $aspectAnnotation->priority, $options);
                                    }
                                }
                            }
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Unknown pointCutType %s', $pointCut->type));
                    }
                }
            }
        }
        self::$loaded = true;
    }
}
