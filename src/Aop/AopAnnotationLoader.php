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

class AopAnnotationLoader
{
    private static array $map = [];

    private function __construct()
    {
    }

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map)
    {
        self::$map = $map;
    }

    public static function saveMap(string $fileName)
    {
        file_put_contents($fileName, '<?php return ' . var_export(self::$map, true) . ';');
    }

    public static function load(?string $fileName = null, bool $force = true)
    {
        if(!$force && self::$map)
        {
            return;
        }
        if (null !== $fileName && is_file($fileName))
        {
            self::$map = $map = include $fileName;
            foreach ($map as $class => $list1)
            {
                foreach ($list1['methods'] as $method => $list2)
                {
                    foreach ($list2['before'] ?? [] as $item)
                    {
                        $callback = $item['callback'];
                        $callback[0] = new $callback[0]();
                        AopManager::addBefore($class, $method, $callback, $item['priority'], $item['options']);
                    }
                    foreach ($list2['after'] ?? [] as $item)
                    {
                        $callback = $item['callback'];
                        $callback[0] = new $callback[0]();
                        AopManager::addAfter($class, $method, $callback, $item['priority'], $item['options']);
                    }
                    foreach ($list2['around'] ?? [] as $item)
                    {
                        $callback = $item['callback'];
                        $callback[0] = new $callback[0]();
                        AopManager::addAround($class, $method, $callback, $item['priority'], $item['options']);
                    }
                    foreach ($list2['afterReturning'] ?? [] as $item)
                    {
                        $callback = $item['callback'];
                        $callback[0] = new $callback[0]();
                        AopManager::addAfterReturning($class, $method, $callback, $item['priority'], $item['options']);
                    }
                    foreach ($list2['afterThrowing'] ?? [] as $item)
                    {
                        $callback = $item['callback'];
                        $callback[0] = new $callback[0]();
                        AopManager::addAfterThrowing($class, $method, $callback, $item['priority'], $item['options']);
                    }
                }
            }
        }
        else
        {
            $map = [];
            foreach (AnnotationManager::getAnnotationPoints(Aspect::class) as $item)
            {
                /** @var Aspect $aspectAnnotation */
                $aspectAnnotation = $item->getAnnotation();
                $className = $item->getClass();
                $classObject = new $className();
                foreach (AnnotationManager::getMethodsAnnotations($className, PointCut::class) as $methodName => $pointCuts)
                {
                    $callback = [$classObject, $methodName];
                    $mapItem = [
                        'callback' => [$className, $methodName],
                        'priority' => $aspectAnnotation->priority,
                    ];
                    /** @var Before $beforeAnnotation */
                    $beforeAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, Before::class)[0] ?? null;
                    /** @var After $beforeAnnotation */
                    $afterAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, After::class)[0] ?? null;
                    /** @var Around $beforeAnnotation */
                    $aroundAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, Around::class)[0] ?? null;
                    /** @var AfterReturning $beforeAnnotation */
                    $afterReturningAnnotation = AnnotationManager::getMethodAnnotations($className, $methodName, AfterReturning::class)[0] ?? null;
                    /** @var AfterThrowing $beforeAnnotation */
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
                                        $mapItem['options'] = $options;
                                        $map[$class]['methods'][$method]['before'][] = $mapItem;
                                    }
                                    if ($afterAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterAnnotation->toArray(),
                                        ];
                                        AopManager::addAfter($class, $method, $callback, $aspectAnnotation->priority, $options);
                                        $mapItem['options'] = $options;
                                        $map[$class]['methods'][$method]['after'][] = $mapItem;
                                    }
                                    if ($aroundAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $aroundAnnotation->toArray(),
                                        ];
                                        AopManager::addAround($class, $method, $callback, $aspectAnnotation->priority, $options);
                                        $mapItem['options'] = $options;
                                        $map[$class]['methods'][$method]['around'][] = $mapItem;
                                    }
                                    if ($afterReturningAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterReturningAnnotation->toArray(),
                                        ];
                                        AopManager::addAfterReturning($class, $method, $callback, $aspectAnnotation->priority, $options);
                                        $mapItem['options'] = $options;
                                        $map[$class]['methods'][$method]['afterReturning'][] = $mapItem;
                                    }
                                    if ($afterThrowingAnnotation)
                                    {
                                        $options = [
                                            'deny'  => $pointCut->deny,
                                            'extra' => $afterThrowingAnnotation->toArray(),
                                        ];
                                        AopManager::addAfterThrowing($class, $method, $callback, $aspectAnnotation->priority, $options);
                                        $mapItem['options'] = $options;
                                        $map[$class]['methods'][$method]['afterThrowing'][] = $mapItem;
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
                                            $mapItem['options'] = $options;
                                            $map[$class]['methods'][$method]['before'][] = $mapItem;
                                        }
                                        if ($afterAnnotation)
                                        {
                                            $options = [
                                                'deny'  => $pointCut->deny,
                                                'extra' => $afterAnnotation->toArray(),
                                            ];
                                            AopManager::addAfter($class, $method, $callback, $aspectAnnotation->priority, $options);
                                            $mapItem['options'] = $options;
                                            $map[$class]['methods'][$method]['after'][] = $mapItem;
                                        }
                                        if ($aroundAnnotation)
                                        {
                                            $options = [
                                                'deny'  => $pointCut->deny,
                                                'extra' => $aroundAnnotation->toArray(),
                                            ];
                                            AopManager::addAround($class, $method, $callback, $aspectAnnotation->priority, $options);
                                            $mapItem['options'] = $options;
                                            $map[$class]['methods'][$method]['around'][] = $mapItem;
                                        }
                                        if ($afterReturningAnnotation)
                                        {
                                            $options = [
                                                'deny'  => $pointCut->deny,
                                                'extra' => $afterReturningAnnotation->toArray(),
                                            ];
                                            AopManager::addAfterReturning($class, $method, $callback, $aspectAnnotation->priority, $options);
                                            $mapItem['options'] = $options;
                                            $map[$class]['methods'][$method]['afterReturning'][] = $mapItem;
                                        }
                                        if ($afterThrowingAnnotation)
                                        {
                                            $options = [
                                                'deny'  => $pointCut->deny,
                                                'extra' => $afterThrowingAnnotation->toArray(),
                                            ];
                                            AopManager::addAfterThrowing($class, $method, $callback, $aspectAnnotation->priority, $options);
                                            $mapItem['options'] = $options;
                                            $map[$class]['methods'][$method]['afterThrowing'][] = $mapItem;
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
            self::$map = $map;
        }
    }
}
