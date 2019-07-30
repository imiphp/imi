<?php
namespace Imi\Bean\Annotation\Model;

use Imi\Bean\Annotation\Model\ClassAnnotationRelation;

class AnnotationRelation
{
    /**
     * 类关联列表
     *
     * @var \Imi\Bean\Annotation\Model\ClassAnnotationRelation[]
     */
    private $classRelations = [];

    /**
     * 方法关联列表
     *
     * @var \Imi\Bean\Annotation\Model\MethodAnnotationRelation[]
     */
    private $methodRelations = [];

    /**
     * 属性关联列表
     *
     * @var \Imi\Bean\Annotation\Model\PropertyAnnotationRelation[]
     */
    private $propertieRelations = [];

    /**
     * 常量关联列表
     *
     * @var \Imi\Bean\Annotation\Model\ConstantAnnotationRelation[]
     */
    private $constantRelations = [];

    /**
     * 所有关联列表
     *
     * @var \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    private $allRelations = [];

    /**
     * Get 类关联列表
     *
     * @return \Imi\Bean\Annotation\Model\ClassAnnotationRelation[]
     */ 
    public function getClassRelations()
    {
        return $this->classRelations;
    }

    /**
     * 增加类关联
     *
     * @param \Imi\Bean\Annotation\Model\ClassAnnotationRelation $relation
     * @return void
     */
    public function addClassRelation(ClassAnnotationRelation $relation)
    {
        $class = get_class($relation->getAnnotation());
        $this->classRelations[$class][] = $relation;
        $this->allRelations[$class] = null;
    }

    /**
     * Get 方法关联列表
     *
     * @return \Imi\Bean\Annotation\Model\MethodAnnotationRelation[]
     */ 
    public function getMethodRelations()
    {
        return $this->methodRelations;
    }

    /**
     * 增加方法关联
     *
     * @param \Imi\Bean\Annotation\Model\MethodAnnotationRelation $relation
     * @return void
     */
    public function addMethodRelation(MethodAnnotationRelation $relation)
    {
        $class = get_class($relation->getAnnotation());
        $this->methodRelations[$class][] = $relation;
        $this->allRelations[$class] = null;
    }

    /**
     * Get 属性关联列表
     *
     * @return \Imi\Bean\Annotation\Model\PropertyAnnotationRelation[]
     */ 
    public function getPropertieRelations()
    {
        return $this->propertieRelations;
    }

    /**
     * 增加属性关联
     *
     * @param \Imi\Bean\Annotation\Model\PropertyAnnotationRelation $relation
     * @return void
     */
    public function addPropertyRelation(PropertyAnnotationRelation $relation)
    {
        $class = get_class($relation->getAnnotation());
        $this->propertieRelations[$class][] = $relation;
        $this->allRelations[$class] = null;
    }

    /**
     * Get 常量关联列表
     *
     * @return \Imi\Bean\Annotation\Model\ConstantAnnotationRelation[]
     */ 
    public function getConstantRelations()
    {
        return $this->constantRelations;
    }

    /**
     * 增加常量关联
     *
     * @param \Imi\Bean\Annotation\Model\ConstantAnnotationRelation $relation
     * @return void
     */
    public function addConstantRelation(ConstantAnnotationRelation $relation)
    {
        $class = get_class($relation->getAnnotation());
        $this->constantRelations[$class][] = $relation;
        $this->allRelations[$class] = null;
    }

    /**
     * 获取所有注解列表
     * 如果 $where 为 null，则返回指定注解列表
     *
     * @param string $className
     * @param string|null $where
     * @return \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    public function getAll($className, $where = null)
    {
        if(null === $where)
        {
            if(!isset($this->allRelations[$className]))
            {
                $this->allRelations[$className] = array_merge(
                    $this->classRelations[$className] ?? [],
                    $this->methodRelations[$className] ?? [],
                    $this->propertieRelations[$className] ?? [],
                    $this->constantRelations[$className] ?? []
                );
            }
            return $this->allRelations[$className];
        }
        return $this->{$where . 'Relations'}[$className] ?? [];
    }

    /**
     * 移除类注解关联
     *
     * @param string $annotationClassName
     * @param string $className
     * @return void
     */
    public function removeClassRelation($annotationClassName, $className)
    {
        if(isset($this->classRelations[$annotationClassName]))
        {
            foreach($this->classRelations[$annotationClassName] as $i => $relation)
            {
                if($relation->getClass() === $className)
                {
                    unset($this->classRelations[$annotationClassName][$i]);
                }
            }
            $this->classRelations[$annotationClassName] = array_values($this->classRelations[$annotationClassName]);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除类注解关联
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $methodName
     * @return void
     */
    public function removeMethodRelation(string $annotationClassName, string $className, string $methodName)
    {
        if(isset($this->methodRelations[$annotationClassName]))
        {
            foreach($this->methodRelations[$annotationClassName] as $i => $relation)
            {
                if($relation->getClass() === $className && $relation->getMethod() === $methodName)
                {
                    unset($this->methodRelations[$annotationClassName][$i]);
                }
            }
            $this->methodRelations[$annotationClassName] = array_values($this->methodRelations[$annotationClassName]);
        }
        $this->allRelations[$annotationClassName] = null;
    }

    /**
     * 移除类注解关联
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $propertyName
     * @return void
     */
    public function removePropertyRelation(string $annotationClassName, string $className, string $propertyName)
    {
        if(isset($this->propertyRelations[$annotationClassName]))
        {
            foreach($this->propertyRelations[$annotationClassName] as $i => $relation)
            {
                if($relation->getClass() === $className && $relation->getProperty() === $propertyName)
                {
                    unset($this->propertyRelations[$annotationClassName][$i]);
                }
            }
            $this->propertyRelations[$annotationClassName] = array_values($this->propertyRelations[$annotationClassName]);
        }
        $this->allRelations[$annotationClassName] = null;
    }
    
    /**
     * 移除类注解关联
     *
     * @param string $annotationClassName
     * @param string $className
     * @param string $constantName
     * @return void
     */
    public function removeConstantRelation(string $annotationClassName, string $className, string $constantName)
    {
        if(isset($this->constantRelations[$annotationClassName]))
        {
            foreach($this->constantRelations[$annotationClassName] as $i => $relation)
            {
                if($relation->getClass() === $className && $relation->getConstant() === $constantName)
                {
                    unset($this->constantRelations[$annotationClassName][$i]);
                }
            }
            $this->constantRelations[$annotationClassName] = array_values($this->constantRelations[$annotationClassName]);
        }
        $this->allRelations[$annotationClassName] = null;
    }
}