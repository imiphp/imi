<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

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

    /**
     * 注解类与类、方法、属性的关联关系.
     *
     * @var \Imi\Bean\Annotation\Model\AnnotationRelation
     */
    private static AnnotationRelation $annotationRelation;

    private function __construct()
    {
    }

    public static function init(): void
    {
        static::$annotationRelation = new AnnotationRelation();
    }

    /**
     * 设置注解列表.
     *
     * @param array $annotations
     *
     * @return void
     */
    public static function setAnnotations(array $annotations): void
    {
        static::$annotations = $annotations;
    }

    /**
     * 获取注解列表.
     *
     * @return array
     */
    public static function getAnnotations(): array
    {
        return static::$annotations;
    }

    /**
     * 设置关联关系数据.
     *
     * @param \Imi\Bean\Annotation\Model\AnnotationRelation $data
     *
     * @return void
     */
    public static function setAnnotationRelation(AnnotationRelation $data): void
    {
        static::$annotationRelation = $data;
    }

    /**
     * 获取关联关系数据.
     *
     * @return \Imi\Bean\Annotation\Model\AnnotationRelation
     */
    public static function getAnnotationRelation(): AnnotationRelation
    {
        return static::$annotationRelation;
    }

    /**
     * 增加类注解.
     *
     * @param string                    $className
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function addClassAnnotations(string $className, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addClassAnnotations($annotations);
        foreach ($annotations as $annotation)
        {
            static::$annotationRelation->addClassRelation(new ClassAnnotationRelation($className, $annotation));
        }
    }

    /**
     * 设置类注解.
     *
     * @param string                    $className
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function setClassAnnotations(string $className, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            static::$annotationRelation->removeClassRelation($className);
            $staticAnnotations[$className]->clearClassAnnotations();
        }
        static::addClassAnnotations($className, ...$annotations);
    }

    /**
     * 增加方法注解.
     *
     * @param string                    $className
     * @param string                    $methodName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function addMethodAnnotations(string $className, string $methodName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addMethodAnnotations($methodName, $annotations);
        foreach ($annotations as $annotation)
        {
            static::$annotationRelation->addMethodRelation(new MethodAnnotationRelation($className, $methodName, $annotation));
        }
    }

    /**
     * 设置方法注解.
     *
     * @param string                    $className
     * @param string                    $methodName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function setMethodAnnotations(string $className, string $methodName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearMethodAnnotations($methodName);
            static::$annotationRelation->removeMethodRelation($className, $methodName);
        }
        static::addMethodAnnotations($className, $methodName, ...$annotations);
    }

    /**
     * 增加属性注解.
     *
     * @param string                    $className
     * @param string                    $propertyName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function addPropertyAnnotations(string $className, string $propertyName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addPropertyAnnotations($propertyName, $annotations);
        foreach ($annotations as $annotation)
        {
            static::$annotationRelation->addPropertyRelation(new PropertyAnnotationRelation($className, $propertyName, $annotation));
        }
    }

    /**
     * 设置属性注解.
     *
     * @param string                    $className
     * @param string                    $propertyName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function setPropertyAnnotations(string $className, string $propertyName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearPropertyAnnotations($propertyName);
            static::$annotationRelation->removePropertyRelation($className, $propertyName);
        }
        static::addPropertyAnnotations($className, $propertyName, ...$annotations);
    }

    /**
     * 增加常量注解.
     *
     * @param string                    $className
     * @param string                    $constantName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function addConstantAnnotations(string $className, string $constantName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
        }
        else
        {
            $staticAnnotations[$className] = $classAnnotation = new ClassAnnotation($className);
        }
        $classAnnotation->addConstantAnnotations($constantName, $annotations);
        foreach ($annotations as $annotation)
        {
            static::$annotationRelation->addConstantRelation(new ConstantAnnotationRelation($className, $constantName, $annotation));
        }
    }

    /**
     * 设置常量注解.
     *
     * @param string                    $className
     * @param string                    $constantName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     *
     * @return void
     */
    public static function setConstantAnnotations(string $className, string $constantName, Base ...$annotations): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $staticAnnotations[$className]->clearConstantAnnotations($constantName);
            static::$annotationRelation->removeConstantRelation($className, $constantName);
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
        return static::$annotationRelation->getAll($annotationClassName, $where);
    }

    /**
     * 获取类注解
     * 可选，是否只获取指定类型注解.
     *
     * @param string      $className
     * @param string|null $annotationClassName
     *
     * @return array
     */
    public static function getClassAnnotations(string $className, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getClassAnnotations();
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
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
     * @param string      $className
     * @param string      $methodName
     * @param string|null $annotationClassName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public static function getMethodAnnotations(string $className, string $methodName, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getMethodAnnotations($methodName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
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
     * @param string      $className
     * @param string      $propertyName
     * @param string|null $annotationClassName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public static function getPropertyAnnotations(string $className, string $propertyName, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getPropertyAnnotations($propertyName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
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
     * @param string      $className
     * @param string      $constantName
     * @param string|null $annotationClassName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public static function getConstantAnnotations(string $className, string $constantName, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
        if (!isset($staticAnnotations[$className]))
        {
            return [];
        }
        $annotations = $staticAnnotations[$className]->getConstantAnnotations($constantName);
        if (null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    $result[] = $annotation;
                }
            }

            return $result;
        }
    }

    /**
     * 获取一个类中所有包含指定注解的方法.
     *
     * @param string $className
     * @param string $annotationClassName
     *
     * @return array
     */
    public static function getMethodsAnnotations(string $className, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
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
        foreach ($annotationList as $methodName => $annotations)
        {
            $resultMethodItem = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    $resultMethodItem[] = $annotation;
                }
            }
            if ($resultMethodItem)
            {
                $result[$methodName] = $resultMethodItem;
            }
        }

        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的属性.
     *
     * @param string $className
     * @param string $annotationClassName
     *
     * @return array
     */
    public static function getPropertiesAnnotations(string $className, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
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
        foreach ($annotationList as $propertyName => $annotations)
        {
            $resultPropertyItem = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    $resultPropertyItem[] = $annotation;
                }
            }
            if ($resultPropertyItem)
            {
                $result[$propertyName] = $resultPropertyItem;
            }
        }

        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的常量.
     *
     * @param string $className
     * @param string $annotationClassName
     *
     * @return array
     */
    public static function getConstantsAnnotations(string $className, ?string $annotationClassName = null): array
    {
        $staticAnnotations = &static::$annotations;
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
        foreach ($annotationList as $constantName => $annotations)
        {
            $resultConstantItem = [];
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof $annotationClassName)
                {
                    $resultConstantItem[] = $annotation;
                }
            }
            if ($resultConstantItem)
            {
                $result[$constantName] = $resultConstantItem;
            }
        }

        return $result;
    }

    /**
     * 清空类所有类、属性、方法、常量注解.
     *
     * @param string $className
     *
     * @return void
     */
    public static function clearClassAllAnnotations(string $className): void
    {
        $staticAnnotations = &static::$annotations;
        if (isset($staticAnnotations[$className]))
        {
            $classAnnotation = $staticAnnotations[$className];
            foreach ($classAnnotation->getClassAnnotations() as $annotation)
            {
                static::$annotationRelation->removeClassRelation(\get_class($annotation), $className);
                if (null !== ($aliases = $annotation->getAlias()))
                {
                    foreach ((array) $aliases as $alias)
                    {
                        static::$annotationRelation->removeClassRelation($alias, $className);
                    }
                }
            }
            foreach ($classAnnotation->getMethodAnnotations() as $methodName => $annotations)
            {
                foreach ($annotations as $annotation)
                {
                    static::$annotationRelation->removeMethodRelation(\get_class($annotation), $className, $methodName);
                    if (null !== ($aliases = $annotation->getAlias()))
                    {
                        foreach ((array) $aliases as $alias)
                        {
                            static::$annotationRelation->removeMethodRelation($alias, $className, $methodName);
                        }
                    }
                }
            }
            foreach ($classAnnotation->getPropertyAnnotations() as $propertyName => $annotations)
            {
                foreach ($annotations as $annotation)
                {
                    static::$annotationRelation->removePropertyAnnotationRelation(\get_class($annotation), $className, $propertyName);
                    if (null !== ($aliases = $annotation->getAlias()))
                    {
                        foreach ((array) $aliases as $alias)
                        {
                            static::$annotationRelation->removePropertyRelation($alias, $className, $propertyName);
                        }
                    }
                }
            }
            foreach ($classAnnotation->getConstantAnnotations() as $constName => $annotations)
            {
                foreach ($annotations as $annotation)
                {
                    static::$annotationRelation->removeConstantAnnotationRelation(\get_class($annotation), $className, $constName);
                    if (null !== ($aliases = $annotation->getAlias()))
                    {
                        foreach ((array) $aliases as $alias)
                        {
                            static::$annotationRelation->removeConstantRelation($alias, $className, $constName);
                        }
                    }
                }
            }
            unset($staticAnnotations[$className]);
        }
    }
}
