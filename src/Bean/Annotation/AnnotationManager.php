<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Model\AnnotationRelation;
use Imi\Bean\Annotation\Model\ClassAnnotation;
use Imi\Bean\Annotation\Model\ClassAnnotationRelation;
use Imi\Bean\Annotation\Model\ConstantAnnotationRelation;
use Imi\Bean\Annotation\Model\MethodAnnotationRelation;
use Imi\Bean\Annotation\Model\PropertyAnnotationRelation;

class AnnotationManager
{
    /**
     * 注解列表.
     *
     * @var \Imi\Bean\Annotation\Model\ClassAnnotation[]
     */
    private static array $annotations = [];

    private static array $annotationsCache = [];

    /**
     * 注解类与类、方法、属性的关联关系.
     */
    private static AnnotationRelation $annotationRelation;

    private static bool $removeWhenset = true;

    private function __construct()
    {
    }

    public static function init(): void
    {
        self::$annotationRelation = new AnnotationRelation();
    }

    public static function getRemoveWhenset(): bool
    {
        return self::$removeWhenset;
    }

    public static function setRemoveWhenset(bool $removeWhenset): void
    {
        self::$removeWhenset = $removeWhenset;
    }

    public static function generateAnnotationsCache(): array
    {
        $annotationsCache = self::$annotations;
        foreach ($annotationsCache as &$item)
        {
            $item = serialize($item);
        }

        return $annotationsCache;
    }

    public static function getAnnotationsCache(): array
    {
        return self::$annotationsCache;
    }

    public static function setAnnotationsCache(array $annotationsCache): void
    {
        self::$annotationsCache = $annotationsCache;
    }

    /**
     * 设置注解列表.
     */
    public static function setAnnotations(array $annotations): void
    {
        self::$annotations = $annotations;
    }

    /**
     * 获取注解列表.
     */
    public static function getAnnotations(): array
    {
        return self::$annotations;
    }

    /**
     * 设置关联关系数据.
     */
    public static function setAnnotationRelation(AnnotationRelation $data): void
    {
        self::$annotationRelation = $data;
    }

    /**
     * 获取关联关系数据.
     */
    public static function getAnnotationRelation(): AnnotationRelation
    {
        return self::$annotationRelation;
    }

    /**
     * 增加类注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function addClassAnnotations(string $className, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        elseif (isset(self::$annotationsCache[$className]))
        {
            $staticAnnotations[$className] = $classAnnotation = unserialize(self::$annotationsCache[$className]);
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addClassAnnotations($annotations);
        foreach ($annotations as $annotation)
        {
            self::$annotationRelation->addClassRelation(new ClassAnnotationRelation($className, $annotation));
        }
    }

    /**
     * 设置类注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function setClassAnnotations(string $className, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            self::$annotationRelation->removeClassRelation($className);
            if (self::$removeWhenset)
            {
                $staticAnnotations[$className]->clearClassAnnotations();
            }
        }
        static::addClassAnnotations($className, ...$annotations);
    }

    /**
     * 增加方法注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function addMethodAnnotations(string $className, string $methodName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        elseif (isset(self::$annotationsCache[$className]))
        {
            $staticAnnotations[$className] = $classAnnotation = unserialize(self::$annotationsCache[$className]);
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addMethodAnnotations($methodName, $annotations);
        foreach ($annotations as $annotation)
        {
            self::$annotationRelation->addMethodRelation(new MethodAnnotationRelation($className, $methodName, $annotation));
        }
    }

    /**
     * 设置方法注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function setMethodAnnotations(string $className, string $methodName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearMethodAnnotations($methodName);
            if (self::$removeWhenset)
            {
                self::$annotationRelation->removeMethodRelation($className, $methodName);
            }
        }
        static::addMethodAnnotations($className, $methodName, ...$annotations);
    }

    /**
     * 增加属性注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function addPropertyAnnotations(string $className, string $propertyName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        elseif (isset(self::$annotationsCache[$className]))
        {
            $staticAnnotations[$className] = $classAnnotation = unserialize(self::$annotationsCache[$className]);
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addPropertyAnnotations($propertyName, $annotations);
        foreach ($annotations as $annotation)
        {
            self::$annotationRelation->addPropertyRelation(new PropertyAnnotationRelation($className, $propertyName, $annotation));
        }
    }

    /**
     * 设置属性注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function setPropertyAnnotations(string $className, string $propertyName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearPropertyAnnotations($propertyName);
            if (self::$removeWhenset)
            {
                self::$annotationRelation->removePropertyRelation($className, $propertyName);
            }
        }
        static::addPropertyAnnotations($className, $propertyName, ...$annotations);
    }

    /**
     * 增加常量注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function addConstantAnnotations(string $className, string $constantName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        elseif (isset(self::$annotationsCache[$className]))
        {
            $staticAnnotations[$className] = $classAnnotation = unserialize(self::$annotationsCache[$className]);
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addConstantAnnotations($constantName, $annotations);
        foreach ($annotations as $annotation)
        {
            self::$annotationRelation->addConstantRelation(new ConstantAnnotationRelation($className, $constantName, $annotation));
        }
    }

    /**
     * 设置常量注解.
     *
     * @param \Imi\Bean\Annotation\Base ...$annotations
     */
    public static function setConstantAnnotations(string $className, string $constantName, Base ...$annotations): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearConstantAnnotations($constantName);
            if (self::$removeWhenset)
            {
                self::$annotationRelation->removeConstantRelation($className, $constantName);
            }
        }
        static::addConstantAnnotations($className, $constantName, ...$annotations);
    }

    /**
     * 获取注解使用点.
     *
     * @param string      $annotationClassName 注解类名
     * @param string|null $where               null/class/method/property/constant
     *
     * @return \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    public static function getAnnotationPoints(string $annotationClassName, ?string $where = null): array
    {
        return self::$annotationRelation->getAll($annotationClassName, $where);
    }

    /**
     * 获取类注解
     * 可选，是否只获取指定类型注解.
     *
     * @param string|array|null $annotationClassName
     *
     * ]@return array|\Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]]
     */
    public static function getClassAnnotations(string $className, $annotationClassName = null, bool $autoAnalysis = true, bool $onlyFirst = false)
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getClassAnnotations();
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        elseif (\is_array($annotationClassName))
        {
            $result = [];
            foreach ($annotationClassName as $name)
            {
                if ($onlyFirst)
                {
                    $result[$name] = null;
                }
                else
                {
                    $result[$name] = [];
                }
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $name)
                    {
                        if ($onlyFirst)
                        {
                            $result[$name] = $annotation;
                            break;
                        }
                        else
                        {
                            $result[$name][] = $annotation;
                        }
                    }
                }
            }

            return $result;
        }
        else
        {
            if ($onlyFirst)
            {
                $result = null;
            }
            else
            {
                $result = [];
            }
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    if ($onlyFirst)
                    {
                        return $annotation;
                    }
                    $result[] = $annotation;
                }
            }

            return $result;
        }
    }

    /**
     * 获取指定方法注解
     * 可选，是否只获取指定类型注解.
     *
     * @param string|array|null $annotationClassName
     *
     * ]@return array|\Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]]
     */
    public static function getMethodAnnotations(string $className, string $methodName, $annotationClassName = null, bool $autoAnalysis = true, bool $onlyFirst = false)
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getMethodAnnotations($methodName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        elseif (\is_array($annotationClassName))
        {
            $result = [];
            foreach ($annotationClassName as $name)
            {
                if ($onlyFirst)
                {
                    $result[$name] = null;
                }
                else
                {
                    $result[$name] = [];
                }
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $name)
                    {
                        if ($onlyFirst)
                        {
                            $result[$name] = $annotation;
                            break;
                        }
                        else
                        {
                            $result[$name][] = $annotation;
                        }
                    }
                }
            }

            return $result;
        }
        else
        {
            if ($onlyFirst)
            {
                $result = null;
            }
            else
            {
                $result = [];
            }
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    if ($onlyFirst)
                    {
                        return $annotation;
                    }
                    $result[] = $annotation;
                }
            }

            return $result;
        }
    }

    /**
     * 获取指定属性注解
     * 可选，是否只获取指定类型注解.
     *
     * @param string|array|null $annotationClassName
     *
     * ]@return array|\Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]]
     */
    public static function getPropertyAnnotations(string $className, string $propertyName, $annotationClassName = null, bool $autoAnalysis = true, bool $onlyFirst = false)
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getPropertyAnnotations($propertyName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        elseif (\is_array($annotationClassName))
        {
            $result = [];
            foreach ($annotationClassName as $name)
            {
                if ($onlyFirst)
                {
                    $result[$name] = null;
                }
                else
                {
                    $result[$name] = [];
                }
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $name)
                    {
                        if ($onlyFirst)
                        {
                            $result[$name] = $annotation;
                            break;
                        }
                        else
                        {
                            $result[$name][] = $annotation;
                        }
                    }
                }
            }

            return $result;
        }
        else
        {
            if ($onlyFirst)
            {
                $result = null;
            }
            else
            {
                $result = [];
            }
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    if ($onlyFirst)
                    {
                        return $annotation;
                    }
                    $result[] = $annotation;
                }
            }

            return $result;
        }
    }

    /**
     * 获取指定常量注解
     * 可选，是否只获取指定类型注解.
     *
     * @param string|array|null $annotationClassName
     *
     * ]@return array|\Imi\Bean\Annotation\Base|\Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]]
     */
    public static function getConstantAnnotations(string $className, string $constantName, $annotationClassName = null, bool $autoAnalysis = true, bool $onlyFirst = false)
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getConstantAnnotations($constantName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        elseif (\is_array($annotationClassName))
        {
            $result = [];
            foreach ($annotationClassName as $name)
            {
                if ($onlyFirst)
                {
                    $result[$name] = null;
                }
                else
                {
                    $result[$name] = [];
                }
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $name)
                    {
                        if ($onlyFirst)
                        {
                            $result[$name] = $annotation;
                            break;
                        }
                        else
                        {
                            $result[$name][] = $annotation;
                        }
                    }
                }
            }

            return $result;
        }
        else
        {
            if ($onlyFirst)
            {
                $result = null;
            }
            else
            {
                $result = [];
            }
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    if ($onlyFirst)
                    {
                        return $annotation;
                    }
                    $result[] = $annotation;
                }
            }

            return $result;
        }
    }

    /**
     * 获取一个类中所有包含指定注解的方法.
     *
     * @param string|array|null $annotationClassName
     */
    public static function getMethodsAnnotations(string $className, $annotationClassName = null, bool $autoAnalysis = true): array
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotationList = $staticAnnotations[$className]->getMethodAnnotations();
        if (null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        if (\is_array($annotationClassName))
        {
            foreach ($annotationClassName as $name)
            {
                $result[$name] = [];
                foreach ($annotationList as $methodName => $annotations)
                {
                    $items = [];
                    foreach ($annotations as $annotation)
                    {
                        if ($annotation instanceof $name)
                        {
                            $items[] = $annotation;
                        }
                    }
                    if ($items)
                    {
                        $result[$name][$methodName] = $items;
                    }
                }
            }
        }
        else
        {
            foreach ($annotationList as $methodName => $annotations)
            {
                $items = [];
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $annotationClassName)
                    {
                        $items[] = $annotation;
                    }
                }
                if ($items)
                {
                    $result[$methodName] = $items;
                }
            }
        }

        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的属性.
     *
     * @param string|array|null $annotationClassName
     */
    public static function getPropertiesAnnotations(string $className, $annotationClassName = null, bool $autoAnalysis = true): array
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotationList = $staticAnnotations[$className]->getPropertyAnnotations();
        if (null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        if (\is_array($annotationClassName))
        {
            foreach ($annotationClassName as $name)
            {
                $result[$name] = [];
                foreach ($annotationList as $propertyName => $annotations)
                {
                    $items = [];
                    foreach ($annotations as $annotation)
                    {
                        if ($annotation instanceof $name)
                        {
                            $items[] = $annotation;
                        }
                    }
                    if ($items)
                    {
                        $result[$name][$propertyName] = $items;
                    }
                }
            }
        }
        else
        {
            foreach ($annotationList as $propertyName => $annotations)
            {
                $items = [];
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $annotationClassName)
                    {
                        $items[] = $annotation;
                    }
                }
                if ($items)
                {
                    $result[$propertyName] = $items;
                }
            }
        }

        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的常量.
     *
     * @param string|array|null $annotationClassName
     */
    public static function getConstantsAnnotations(string $className, $annotationClassName = null, bool $autoAnalysis = true): array
    {
        $staticAnnotations = &self::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            if (isset(self::$annotationsCache[$className]))
            {
                $staticAnnotations[$className] = unserialize(self::$annotationsCache[$className]);
            }
            elseif ($autoAnalysis)
            {
                $parser = Annotation::getInstance()->getParser();
                $parser->parse($className);
                $parser->execParse($className);
            }
        }
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotationList = $staticAnnotations[$className]->getConstantAnnotations();
        if (null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        if (\is_array($annotationClassName))
        {
            foreach ($annotationClassName as $name)
            {
                $result[$name] = [];
                foreach ($annotationList as $constantName => $annotations)
                {
                    $items = [];
                    foreach ($annotations as $annotation)
                    {
                        if ($annotation instanceof $name)
                        {
                            $items[] = $annotation;
                        }
                    }
                    if ($items)
                    {
                        $result[$name][$constantName] = $items;
                    }
                }
            }
        }
        else
        {
            foreach ($annotationList as $constantName => $annotations)
            {
                $items = [];
                foreach ($annotations as $annotation)
                {
                    if ($annotation instanceof $annotationClassName)
                    {
                        $items[] = $annotation;
                    }
                }
                if ($items)
                {
                    $result[$constantName] = $items;
                }
            }
        }

        return $result;
    }

    /**
     * 清空类所有类、属性、方法、常量注解.
     */
    public static function clearClassAllAnnotations(string $className): void
    {
        $staticAnnotations = &self::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
            self::$annotationRelation->removeClassRelation($className);
            if ($list = $classAnnotation->getMethodAnnotations())
            {
                self::$annotationRelation->removeMethodRelation($className, array_keys($list));
            }
            if ($list = $classAnnotation->getPropertyAnnotations())
            {
                self::$annotationRelation->removePropertyRelation($className, array_keys($list));
            }
            if ($list = $classAnnotation->getConstantAnnotations())
            {
                self::$annotationRelation->removeConstantRelation($className, array_keys($list));
            }
            unset($staticAnnotations[$className]);
        }
    }
}
