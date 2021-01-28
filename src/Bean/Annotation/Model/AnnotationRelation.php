<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

use Imi\Log\Log;

class AnnotationRelation
{
    /**
     * 类关联列表.
     *
     * @var \Imi\Bean\Annotation\Model\ClassAnnotationRelation[][]
     */
    private array $classRelations = [];

    /**
     * 方法关联列表.
     *
     * @var \Imi\Bean\Annotation\Model\MethodAnnotationRelation[][]
     */
    private array $methodRelations = [];

    /**
     * 属性关联列表.
     *
     * @var \Imi\Bean\Annotation\Model\PropertyAnnotationRelation[][]
     */
    private array $propertyRelations = [];

    /**
     * 常量关联列表.
     *
     * @var \Imi\Bean\Annotation\Model\ConstantAnnotationRelation[][]
     */
    private array $constantRelations = [];

    /**
     * 所有关联列表.
     *
     * @var \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    private array $allRelations = [];

    /**
     * Get 类关联列表.
     *
     * @return \Imi\Bean\Annotation\Model\ClassAnnotationRelation[]
     */
    public function getClassRelations(): array
    {
        return $this->classRelations;
    }

    /**
     * 增加类关联.
     *
     * @param \Imi\Bean\Annotation\Model\ClassAnnotationRelation $relation
     *
     * @return void
     */
    public function addClassRelation(ClassAnnotationRelation $relation)
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        $this->classRelations[$class][] = $relation;
        if (null !== ($alias = $annotation->getAlias()))
        {
            foreach ((array) $alias as $class)
            {
                $this->classRelations[$class][] = $relation;
            }
        }
        $this->allRelations[$class] = null;
    }

    /**
     * Get 方法关联列表.
     *
     * @return \Imi\Bean\Annotation\Model\MethodAnnotationRelation[]
     */
    public function getMethodRelations(): array
    {
        return $this->methodRelations;
    }

    /**
     * 增加方法关联.
     *
     * @param \Imi\Bean\Annotation\Model\MethodAnnotationRelation $relation
     *
     * @return void
     */
    public function addMethodRelation(MethodAnnotationRelation $relation)
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        $this->methodRelations[$class][] = $relation;
        if (null !== ($alias = $annotation->getAlias()))
        {
            foreach ((array) $alias as $class)
            {
                $this->methodRelations[$class][] = $relation;
            }
        }
        $this->allRelations[$class] = null;
    }

    /**
     * Get 属性关联列表.
     *
     * @return \Imi\Bean\Annotation\Model\PropertyAnnotationRelation[]
     */
    public function getpropertyRelations(): array
    {
        return $this->propertyRelations;
    }

    /**
     * 增加属性关联.
     *
     * @param \Imi\Bean\Annotation\Model\PropertyAnnotationRelation $relation
     *
     * @return void
     */
    public function addPropertyRelation(PropertyAnnotationRelation $relation)
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        $this->propertyRelations[$class][] = $relation;
        if (null !== ($alias = $annotation->getAlias()))
        {
            foreach ((array) $alias as $class)
            {
                $this->propertyRelations[$class][] = $relation;
            }
        }
        $this->allRelations[$class] = null;
    }

    /**
     * Get 常量关联列表.
     *
     * @return \Imi\Bean\Annotation\Model\ConstantAnnotationRelation[]
     */
    public function getConstantRelations(): array
    {
        return $this->constantRelations;
    }

    /**
     * 增加常量关联.
     *
     * @param \Imi\Bean\Annotation\Model\ConstantAnnotationRelation $relation
     *
     * @return void
     */
    public function addConstantRelation(ConstantAnnotationRelation $relation)
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        $this->constantRelations[$class][] = $relation;
        if (null !== ($alias = $annotation->getAlias()))
        {
            foreach ((array) $alias as $class)
            {
                $this->constantRelations[$class][] = $relation;
            }
        }
        $this->allRelations[$class] = null;
    }

    /**
     * 获取所有注解列表
     * 如果 $where 为 null，则返回指定注解列表.
     *
     * @param string      $className
     * @param string|null $where
     *
     * @return \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    public function getAll(string $className, ?string $where = null): array
    {
        if (null === $where)
        {
            $allRelations = &$this->allRelations;
            if (!isset($allRelations[$className]))
            {
                $allRelations[$className] = array_merge(
                    $this->classRelations[$className] ?? [],
                    $this->methodRelations[$className] ?? [],
                    $this->propertyRelations[$className] ?? [],
                    $this->constantRelations[$className] ?? []
                );
            }

            return $allRelations[$className];
        }

        return $this->{$where . 'Relations'}[$className] ?? [];
    }

    /**
     * 移除类注解关联.
     *
     * @param string $annotationClassName
     * @param string $className
     *
     * @return void
     */
    public function removeClassAnnotationRelation(string $annotationClassName, string $className)
    {
        $classRelations = &$this->classRelations;
        if (isset($classRelations[$annotationClassName]))
        {
            $classRelationsItem = &$classRelations[$annotationClassName];
            foreach ($classRelationsItem as $i => $relation)
            {
                if ($relation->getClass() === $className)
                {
                    unset($classRelationsItem[$i]);
                }
            }
            $classRelationsItem = array_values($classRelationsItem);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除方法注解关联.
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $methodName
     *
     * @return void
     */
    public function removeMethodAnnotationRelation(string $annotationClassName, string $className, string $methodName)
    {
        $methodRelations = &$this->methodRelations;
        if (isset($methodRelations[$annotationClassName]))
        {
            $methodRelationsItem = &$methodRelations[$annotationClassName];
            foreach ($methodRelationsItem as $i => $relation)
            {
                if ($relation->getClass() === $className && $relation->getMethod() === $methodName)
                {
                    unset($methodRelationsItem[$i]);
                }
            }
            $methodRelationsItem = array_values($methodRelationsItem);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除属性注解关联.
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $propertyName
     *
     * @return void
     */
    public function removePropertyAnnotationRelation(string $annotationClassName, string $className, string $propertyName)
    {
        $propertyRelations = &$this->propertyRelations;
        if (isset($propertyRelations[$annotationClassName]))
        {
            $propertyRelationsItem = &$propertyRelations[$annotationClassName];
            foreach ($propertyRelationsItem as $i => $relation)
            {
                if ($relation->getClass() === $className && $relation->getProperty() === $propertyName)
                {
                    unset($propertyRelationsItem[$i]);
                }
            }
            $propertyRelationsItem = array_values($propertyRelationsItem);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除常量注解关联.
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $constantName
     *
     * @return void
     */
    public function removeConstantAnnotationRelation(string $annotationClassName, string $className, string $constantName)
    {
        $constantRelations = &$this->constantRelations;
        if (isset($constantRelations[$annotationClassName]))
        {
            $constantRelationsItem = &$constantRelations[$annotationClassName];
            foreach ($constantRelationsItem as $i => $relation)
            {
                if ($relation->getClass() === $className && $relation->getConstant() === $constantName)
                {
                    unset($constantRelationsItem[$i]);
                }
            }
            $constantRelationsItem = array_values($constantRelationsItem);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除类所有注解关联.
     *
     * @param string $className
     *
     * @return void
     */
    public function removeClassRelation(string $className)
    {
        $classRelations = &$this->classRelations;
        foreach ($classRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className)
                {
                    unset($list[$i]);
                    $haveUnset = true;
                }
            }
            if ($haveUnset)
            {
                $list = array_values($list);
                $this->allRelations[$annotationClass] = null;
            }
        }
    }

    /**
     * 移除方法所有注解关联.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return void
     */
    public function removeMethodRelation(string $className, string $methodName)
    {
        $methodRelations = &$this->methodRelations;
        foreach ($methodRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && $item->getMethod() === $methodName)
                {
                    unset($list[$i]);
                    $haveUnset = true;
                }
            }
            if ($haveUnset)
            {
                $list = array_values($list);
                $this->allRelations[$annotationClass] = null;
            }
        }
    }

    /**
     * 移除属性所有注解关联.
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return void
     */
    public function removePropertyRelation(string $className, string $propertyName)
    {
        $propertyRelations = &$this->propertyRelations;
        foreach ($propertyRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && $item->getProperty() === $propertyName)
                {
                    unset($list[$i]);
                    $haveUnset = true;
                }
            }
            if ($haveUnset)
            {
                $list = array_values($list);
                $this->allRelations[$annotationClass] = null;
            }
        }
    }

    /**
     * 移除常量所有注解关联.
     *
     * @param string $className
     * @param string $constantName
     *
     * @return void
     */
    public function removeConstantRelation(string $className, string $constantName)
    {
        $constantRelations = &$this->constantRelations;
        foreach ($constantRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && $item->getConstant() === $constantName)
                {
                    unset($list[$i]);
                    $haveUnset = true;
                }
            }
            if ($haveUnset)
            {
                $list = array_values($list);
                $this->allRelations[$annotationClass] = null;
            }
        }
    }
}
