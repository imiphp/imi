<?php
namespace Imi\Bean\Annotation;

abstract class AnnotationManager
{
    /**
     * 注解列表
     *
     * @var array
     */
    private static $annotations = [];

    /**
     * 注解类与类、方法、属性的关联关系
     *
     * @var array
     */
    private static $annotationRelation = [];

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
     * 增加类注解
     *
     * @param string $className
     * @param \Imi\Bean\Annotation\Base ...$annotations
     * @return void
     */
    public static function addClassAnnotations($className, ...$annotations)
    {
        if(isset(static::$annotations[$className]['Class']))
        {
            static::$annotations[$className]['Class'] = array_merge(static::$annotations[$className], $annotations);
        }
        else
        {
            static::$annotations[$className]['Class'] = $annotations;
        }
        foreach($annotations as $annotation)
        {
            static::$annotationRelation[get_class($annotation)]['Class'][] = [
                'type'          =>  'Class',
                'class'         =>  $className,
                'annotation'    =>  $annotation,
            ];
        }
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
        if(isset(static::$annotations[$className]['Methods'][$methodName]))
        {
            static::$annotations[$className]['Methods'][$methodName] = array_merge(static::$annotations[$className]['Methods'][$methodName], $annotations);
        }
        else
        {
            static::$annotations[$className]['Methods'][$methodName] = $annotations;
        }
        foreach($annotations as $annotation)
        {
            static::$annotationRelation[get_class($annotation)]['Method'][] = [
                'type'      =>  'Method',
                'class'     =>  $className,
                'method'    =>  $methodName,
                'annotation'=>  $annotation,
            ];
        }
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
        if(isset(static::$annotations[$className]['Properties'][$propertyName]))
        {
            static::$annotations[$className]['Properties'][$propertyName] = array_merge(static::$annotations[$className]['Properties'][$propertyName], $annotations);
        }
        else
        {
            static::$annotations[$className]['Properties'][$propertyName] = $annotations;
        }
        foreach($annotations as $annotation)
        {
            static::$annotationRelation[get_class($annotation)]['Property'][] = [
                'type'      =>  'Property',
                'class'     =>  $className,
                'property'  =>  $propertyName,
                'annotation'=>  $annotation,
            ];
        }
    }

    /**
     * 获取注解使用点
     *
     * @param string $annotationClassName 注解类名
     * @param string|null $where null/Class/Method/Property
     * @return array
     */
    public static function getAnnotationPoints($annotationClassName, $where = null)
    {
        if(null === $where)
        {
            return array_merge(
                static::$annotationRelation[$annotationClassName]['Class'] ?? [],
                static::$annotationRelation[$annotationClassName]['Method'] ?? [],
                static::$annotationRelation[$annotationClassName]['Property'] ?? []
            );
        }
        else
        {
            return static::$annotationRelation[$annotationClassName][$where] ?? [];
        }
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
        if(!isset(static::$annotations[$className]['Class']))
        {
            return [];
        }
        if(null === $annotationClassName)
        {
            return static::$annotations[$className]['Class'];
        }
        else
        {
            $result = [];
            foreach(static::$annotations[$className]['Class'] as $annotation)
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
        if(!isset(static::$annotations[$className]['Methods'][$methodName]))
        {
            return [];
        }
        if(null === $annotationClassName)
        {
            return static::$annotations[$className]['Methods'][$methodName];
        }
        else
        {
            $result = [];
            foreach(static::$annotations[$className]['Methods'][$methodName] as $annotation)
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
        if(!isset(static::$annotations[$className]['Properties'][$propertyName]))
        {
            return [];
        }
        if(null === $annotationClassName)
        {
            return static::$annotations[$className]['Properties'][$propertyName];
        }
        else
        {
            $result = [];
            foreach(static::$annotations[$className]['Properties'][$propertyName] as $annotation)
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
        if(null === $annotationClassName)
        {
            return static::$annotations[$className]['Methods'] ?? [];
        }
        $result = [];
        foreach(static::$annotations[$className]['Methods'] ?? [] as $methodName => $annotations)
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
        if(null === $annotationClassName)
        {
            return static::$annotations[$className]['Properties'] ?? [];
        }
        $result = [];
        foreach(static::$annotations[$className]['Properties'] ?? [] as $propertyName => $annotations)
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
}