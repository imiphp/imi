<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

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
     */
    private array $allRelations = [];

    private array $cache = [];

    public function generateCache(): array
    {
        $classRelations = $this->classRelations;
        foreach ($classRelations as &$item)
        {
            $item = serialize($item);
        }
        unset($item);

        $methodRelations = $this->methodRelations;
        foreach ($methodRelations as &$item)
        {
            $item = serialize($item);
        }
        unset($item);

        $propertyRelations = $this->propertyRelations;
        foreach ($propertyRelations as &$item)
        {
            $item = serialize($item);
        }
        unset($item);

        $constantRelations = $this->constantRelations;
        foreach ($constantRelations as &$item)
        {
            $item = serialize($item);
        }
        unset($item);

        return [
            'classRelations'    => $classRelations,
            'methodRelations'   => $methodRelations,
            'propertyRelations' => $propertyRelations,
            'constantRelations' => $constantRelations,
        ];
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    public function setCache(array $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * Get 类关联列表.
     *
     * @return \Imi\Bean\Annotation\Model\ClassAnnotationRelation[][]
     */
    public function getClassRelations(): array
    {
        return $this->classRelations;
    }

    /**
     * 增加类关联.
     *
     * @param \Imi\Bean\Annotation\Model\ClassAnnotationRelation $relation
     */
    public function addClassRelation(ClassAnnotationRelation $relation): void
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        if (!isset($this->classRelations[$class]) && isset($this->cache['classRelations'][$class]))
        {
            $this->classRelations[$class] = unserialize($this->cache['classRelations'][$class]);
        }
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
     * @return \Imi\Bean\Annotation\Model\MethodAnnotationRelation[][]
     */
    public function getMethodRelations(): array
    {
        return $this->methodRelations;
    }

    /**
     * 增加方法关联.
     *
     * @param \Imi\Bean\Annotation\Model\MethodAnnotationRelation $relation
     */
    public function addMethodRelation(MethodAnnotationRelation $relation): void
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        if (!isset($this->methodRelations[$class]) && isset($this->cache['methodRelations'][$class]))
        {
            $this->methodRelations[$class] = unserialize($this->cache['methodRelations'][$class]);
        }
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
     * @return \Imi\Bean\Annotation\Model\PropertyAnnotationRelation[][]
     */
    public function getpropertyRelations(): array
    {
        return $this->propertyRelations;
    }

    /**
     * 增加属性关联.
     *
     * @param \Imi\Bean\Annotation\Model\PropertyAnnotationRelation $relation
     */
    public function addPropertyRelation(PropertyAnnotationRelation $relation): void
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        if (!isset($this->propertyRelations[$class]) && isset($this->cache['propertyRelations'][$class]))
        {
            $this->propertyRelations[$class] = unserialize($this->cache['propertyRelations'][$class]);
        }
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
     * @return \Imi\Bean\Annotation\Model\ConstantAnnotationRelation[][]
     */
    public function getConstantRelations(): array
    {
        return $this->constantRelations;
    }

    /**
     * 增加常量关联.
     *
     * @param \Imi\Bean\Annotation\Model\ConstantAnnotationRelation $relation
     */
    public function addConstantRelation(ConstantAnnotationRelation $relation): void
    {
        $annotation = $relation->getAnnotation();
        $class = \get_class($annotation);
        if (!isset($this->constantRelations[$class]) && isset($this->cache['constantRelations'][$class]))
        {
            $this->constantRelations[$class] = unserialize($this->cache['constantRelations'][$class]);
        }
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
     * @return \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    public function getAll(string $className, ?string $where = null): array
    {
        if (null === $where)
        {
            if (!isset($this->classRelations[$className]) && isset($this->cache['classRelations'][$className]))
            {
                $this->classRelations[$className] = unserialize($this->cache['classRelations'][$className]);
            }
            if (!isset($this->methodRelations[$className]) && isset($this->cache['methodRelations'][$className]))
            {
                $this->methodRelations[$className] = unserialize($this->cache['methodRelations'][$className]);
            }
            if (!isset($this->propertyRelations[$className]) && isset($this->cache['propertyRelations'][$className]))
            {
                $this->propertyRelations[$className] = unserialize($this->cache['propertyRelations'][$className]);
            }
            if (!isset($this->constantRelations[$className]) && isset($this->cache['constantRelations'][$className]))
            {
                $this->constantRelations[$className] = unserialize($this->cache['constantRelations'][$className]);
            }
            $allRelations = &$this->allRelations;
            $allRelations[$className] ??= array_merge(
                $this->classRelations[$className] ?? [],
                $this->methodRelations[$className] ?? [],
                $this->propertyRelations[$className] ?? [],
                $this->constantRelations[$className] ?? []
            );

            return $allRelations[$className];
        }

        $fieldName = $where . 'Relations';
        if (!isset($this->$fieldName[$className]) && isset($this->cache[$fieldName][$className]))
        {
            $this->$fieldName[$className] = unserialize($this->cache[$fieldName][$className]);
        }

        return $this->$fieldName[$className] ?? [];
    }

    /**
     * 移除类注解关联.
     */
    public function removeClassAnnotationRelation(string $annotationClassName, string $className): void
    {
        $classRelations = &$this->classRelations;
        if (isset($classRelations[$annotationClassName]))
        {
            $classRelationsItem = &$classRelations[$annotationClassName];
            if ($classRelationsItem)
            {
                foreach ($classRelationsItem as $i => $relation)
                {
                    if ($relation->getClass() === $className)
                    {
                        unset($classRelationsItem[$i]);
                    }
                }
                $classRelationsItem = array_values($classRelationsItem);
            }
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除方法注解关联.
     */
    public function removeMethodAnnotationRelation(string $annotationClassName, string $className, string $methodName): void
    {
        $methodRelations = &$this->methodRelations;
        if (isset($methodRelations[$annotationClassName]))
        {
            $methodRelationsItem = &$methodRelations[$annotationClassName];
            if ($methodRelationsItem)
            {
                foreach ($methodRelationsItem as $i => $relation)
                {
                    if ($relation->getClass() === $className && $relation->getMethod() === $methodName)
                    {
                        unset($methodRelationsItem[$i]);
                    }
                }
                $methodRelationsItem = array_values($methodRelationsItem);
            }
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除属性注解关联.
     */
    public function removePropertyAnnotationRelation(string $annotationClassName, string $className, string $propertyName): void
    {
        $propertyRelations = &$this->propertyRelations;
        if (isset($propertyRelations[$annotationClassName]))
        {
            $propertyRelationsItem = &$propertyRelations[$annotationClassName];
            if ($propertyRelationsItem)
            {
                foreach ($propertyRelationsItem as $i => $relation)
                {
                    if ($relation->getClass() === $className && $relation->getProperty() === $propertyName)
                    {
                        unset($propertyRelationsItem[$i]);
                    }
                }
                $propertyRelationsItem = array_values($propertyRelationsItem);
            }
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除常量注解关联.
     */
    public function removeConstantAnnotationRelation(string $annotationClassName, string $className, string $constantName): void
    {
        $constantRelations = &$this->constantRelations;
        if (isset($constantRelations[$annotationClassName]))
        {
            $constantRelationsItem = &$constantRelations[$annotationClassName];
            if ($constantRelationsItem)
            {
                foreach ($constantRelationsItem as $i => $relation)
                {
                    if ($relation->getClass() === $className && $relation->getConstant() === $constantName)
                    {
                        unset($constantRelationsItem[$i]);
                    }
                }
                $constantRelationsItem = array_values($constantRelationsItem);
            }
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除类所有注解关联.
     *
     * @param string|string[] $className
     */
    public function removeClassRelation($className): void
    {
        $classRelations = &$this->classRelations;
        $className = (array) $className;
        foreach ($classRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if (\in_array($item->getClass(), $className))
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
     * @param string|string[] $methodName
     */
    public function removeMethodRelation(string $className, $methodName): void
    {
        $methodRelations = &$this->methodRelations;
        $methodName = (array) $methodName;
        foreach ($methodRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && \in_array($item->getMethod(), $methodName))
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
     * @param string|string[] $propertyName
     */
    public function removePropertyRelation(string $className, $propertyName): void
    {
        $propertyRelations = &$this->propertyRelations;
        $propertyName = (array) $propertyName;
        foreach ($propertyRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && \in_array($item->getProperty(), $propertyName))
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
     * @param string|string[] $constantName
     */
    public function removeConstantRelation(string $className, $constantName): void
    {
        $constantRelations = &$this->constantRelations;
        $constantName = (array) $constantName;
        foreach ($constantRelations as $annotationClass => &$list)
        {
            $haveUnset = false;
            foreach ($list as $i => $item)
            {
                if ($item->getClass() === $className && \in_array($item->getConstant(), $constantName))
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
