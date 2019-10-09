<?php
namespace Imi\Bean\Annotation;

use Imi\Bean\Annotation\Model\ClassAnnotation;
use Imi\Bean\Annotation\Model\AnnotationRelation;
use Imi\Bean\Annotation\Model\ClassAnnotationRelation;
use Imi\Bean\Annotation\Model\MethodAnnotationRelation;
use Imi\Bean\Annotation\Model\ConstantAnnotationRelation;
use Imi\Bean\Annotation\Model\PropertyAnnotationRelation;

abstract class AnnotationManager
{
    /**
     * 注解列表
     *
     * @var \Imi\Bean\Annotation\Model\ClassAnnotation[]
     */
    private static $annotations = [];

    /**
     * 注解类与类、方法、属性的关联关系
     *
     * @var \Imi\Bean\Annotation\Model\AnnotationRelation
     */
    private static $annotationRelation;

    public static function init()
    {
        static::$annotations = [];
        static::$annotationRelation = new AnnotationRelation;
    }

    /**
     * 设置注解列表
     *
     * @param array $annotations
     * @return void
     */
    public static function setAnnotations($annotations)
    {
        static::$annotations = $annotations;
    }

    /**
     * 获取注解列表
     *
     * @return array
     */
    public static function getAnnotations()
    {
        return static::$annotations;
    }

    /**
     * 设置关联关系数据
     *
     * @param \Imi\Bean\Annotation\Model\AnnotationRelation $data
     * @return void
     */
    public static function setAnnotationRelation(AnnotationRelation $data)
    {
        static::$annotationRelation = $data;
    }

    /**
     * 获取关联关系数据
     *
     * @return \Imi\Bean\Annotation\Model\AnnotationRelation
     */
    public static function getAnnotationRelation(): AnnotationRelation
    {
        return static::$annotationRelation;
    }

    /**
     * 增加类注解
     *
     * @param string $className
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function addClassAnnotations($className, ...$annotations)
    {
        if(!isset(static::$annotations[$className]))
        {
            static::$annotations[$className] = new ClassAnnotation($className);
        }
        static::$annotations[$className]->addClassAnnotations($annotations);
        foreach($annotations as $annotation)
        {
            static::$annotationRelation->addClassRelation(new ClassAnnotationRelation($className, $annotation));
        }
    }

    /**
     * 设置类注解
     *
     * @param string $className
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function setClassAnnotations($className, ...$annotations)
    {
        if(isset(static::$annotations[$className]))
        {
            $tmpAnnotations = static::$annotations[$className]->getClassAnnotations();
            foreach($tmpAnnotations as $annotation)
            {
                static::$annotationRelation->removeClassRelation(get_class($annotation), $className);
            }
            unset(static::$annotations[$className]);
        }
        static::addClassAnnotations($className, ...$annotations);
    }

    /**
     * 增加方法注解
     *
     * @param string $className
     * @param string $methodName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function addMethodAnnotations($className, $methodName, ...$annotations)
    {
        if(!isset(static::$annotations[$className]))
        {
            static::$annotations[$className] = new ClassAnnotation($className);
        }
        static::$annotations[$className]->addMethodAnnotations($methodName, $annotations);
        foreach($annotations as $annotation)
        {
            static::$annotationRelation->addMethodRelation(new MethodAnnotationRelation($className, $methodName, $annotation));
        }
    }

    /**
     * 设置方法注解
     *
     * @param string $className
     * @param string $methodName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function setMethodAnnotations($className, $methodName, ...$annotations)
    {
        if(isset(static::$annotations[$className]))
        {
            $tmpAnnotations = static::$annotations[$className]->getMethodAnnotations($methodName);
            foreach($tmpAnnotations as $annotation)
            {
                static::$annotationRelation->removeMethodRelation(get_class($annotation), $className, $methodName);
            }
        }
        static::addMethodAnnotations($className, $methodName, ...$annotations);
    }

    /**
     * 增加属性注解
     *
     * @param string $className
     * @param string $propertyName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function addPropertyAnnotations($className, $propertyName, ...$annotations)
    {
        if(!isset(static::$annotations[$className]))
        {
            static::$annotations[$className] = new ClassAnnotation($className);
        }
        static::$annotations[$className]->addpropertyAnnotations($propertyName, $annotations);
        foreach($annotations as $annotation)
        {
            static::$annotationRelation->addPropertyRelation(new PropertyAnnotationRelation($className, $propertyName, $annotation));
        }
    }

    /**
     * 设置属性注解
     *
     * @param string $className
     * @param string $propertyName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function setPropertyAnnotations($className, $propertyName, ...$annotations)
    {
        if(isset(static::$annotations[$className]))
        {
            $tmpAnnotations = static::$annotations[$className]->getPropertyAnnotations($propertyName);
            foreach($tmpAnnotations as $annotation)
            {
                static::$annotationRelation->removePropertyRelation(get_class($annotation), $className, $propertyName);
            }
        }
        static::addPropertyAnnotations($className, $propertyName, ...$annotations);
    }

    /**
     * 增加常量注解
     *
     * @param string $className
     * @param string $constantName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function addConstantAnnotations($className, $constantName, ...$annotations)
    {
        if(!isset(static::$annotations[$className]))
        {
            static::$annotations[$className] = new ClassAnnotation($className);
        }
        static::$annotations[$className]->addConstantAnnotations($constantName, $annotations);
        foreach($annotations as $annotation)
        {
            static::$annotationRelation->addConstantRelation(new ConstantAnnotationRelation($className, $constantName, $annotation));
        }
    }

    /**
     * 设置常量注解
     *
     * @param string $className
     * @param string $constantName
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function setConstantAnnotations($className, $constantName, ...$annotations)
    {
        if(isset(static::$annotations[$className]))
        {
            $tmpAnnotations = static::$annotations[$className]->getConstantAnnotations($constantName);
            foreach($tmpAnnotations as $annotation)
            {
                static::$annotationRelation->removeConstantRelation(get_class($annotation), $className, $constantName);
            }
        }
        static::addConstantAnnotations($className, $constantName, ...$annotations);
    }

    /**
     * 获取注解使用点
     *
     * @param string $annotationClassName 注解类名
     * @param string|null $where null/class/method/property
     * @return \Imi\Bean\Annotation\Model\IAnnotationRelation[]
     */
    public static function getAnnotationPoints($annotationClassName, $where = null)
    {
        return static::$annotationRelation->getAll($annotationClassName, $where);
    }

    /**
     * 获取类注解
     * 可选，是否只获取指定类型注解
     *
     * @param string $className
     * @param string|null $annotationClassName
     * @return array
     */
    public static function getClassAnnotations($className, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotations = static::$annotations[$className]->getClassAnnotations();
        if(null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[] = $annotation;
                }
            }
            return $result;
        }
    }

    /**
     * 获取指定方法注解
     * 可选，是否只获取指定类型注解
     *
     * @param string $className
     * @param string $methodName
     * @param string|null $annotationClassName
     * @return void
     */
    public static function getMethodAnnotations($className, $methodName, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotations = static::$annotations[$className]->getMethodAnnotations($methodName);
        if(null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[] = $annotation;
                }
            }
            return $result;
        }
    }

    /**
     * 获取指定属性注解
     * 可选，是否只获取指定类型注解
     *
     * @param string $className
     * @param string $propertyName
     * @param string|null $annotationClassName
     * @return void
     */
    public static function getPropertyAnnotations($className, $propertyName, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotations = static::$annotations[$className]->getPropertyAnnotations($propertyName);
        if(null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[] = $annotation;
                }
            }
            return $result;
        }
    }

    /**
     * 获取指定常量注解
     * 可选，是否只获取指定类型注解
     *
     * @param string $className
     * @param string $constantName
     * @param string|null $annotationClassName
     * @return void
     */
    public static function getConstantAnnotations($className, $constantName, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotations = static::$annotations[$className]->getConstantAnnotations($constantName);
        if(null === $annotationClassName)
        {
            return $annotations;
        }
        else
        {
            $result = [];
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[] = $annotation;
                }
            }
            return $result;
        }
    }

    /**
     * 获取一个类中所有包含指定注解的方法
     *
     * @param string $className
     * @param string $annotationClassName
     * @return array
     */
    public static function getMethodsAnnotations($className, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotationList = static::$annotations[$className]->getMethodAnnotations();
        if(null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        foreach($annotationList as $methodName => $annotations)
        {
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[$methodName][] = $annotation;
                }
            }
        }
        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的属性
     *
     * @param string $className
     * @param string $annotationClassName
     * @return array
     */
    public static function getPropertiesAnnotations($className, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotationList = static::$annotations[$className]->getPropertyAnnotations();
        if(null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        foreach($annotationList as $propertyName => $annotations)
        {
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[$propertyName][] = $annotation;
                }
            }
        }
        return $result;
    }

    /**
     * 获取一个类中所有包含指定注解的常量
     *
     * @param string $className
     * @param string $annotationClassName
     * @return array
     */
    public static function getConstantsAnnotations($className, $annotationClassName = null)
    {
        if(!isset(static::$annotations[$className]))
        {
            return [];
        }
        $annotationList = static::$annotations[$className]->getConstantAnnotations();
        if(null === $annotationClassName)
        {
            return $annotationList;
        }
        $result = [];
        foreach($annotationList as $constantName => $annotations)
        {
            foreach($annotations as $annotation)
            {
                if($annotation instanceof $annotationClassName)
                {
                    $result[$constantName][] = $annotation;
                }
            }
        }
        return $result;
    }

    /**
     * 清空类所有类、属性、方法、常量注解
     *
     * @param string $className
     * @return void
     */
    public static function clearClassAllAnnotations($className)
    {
        if(isset(static::$annotations[$className]))
        {
            foreach(static::$annotations[$className]->getClassAnnotations() as $annotation)
            {
                static::$annotationRelation->removeClassRelation(get_class($annotation), $className);
            }
            foreach(static::$annotations[$className]->getMethodAnnotations() as $methodName => $annotations)
            {
                foreach($annotations as $annotation)
                {
                    static::$annotationRelation->removeMethodRelation(get_class($annotation), $className, $methodName);
                }
            }
            foreach(static::$annotations[$className]->getPropertyAnnotations() as $propertyName => $annotations)
            {
                foreach($annotations as $annotation)
                {
                    static::$annotationRelation->removePropertyRelation(get_class($annotation), $className, $propertyName);
                }
            }
            foreach(static::$annotations[$className]->getConstantAnnotations() as $constName => $annotations)
            {
                foreach($annotations as $annotation)
                {
                    static::$annotationRelation->removeConstantRelation(get_class($annotation), $className, $constName);
                }
            }
            unset(static::$annotations[$className]);
        }
    }

}