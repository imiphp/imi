<?php
namespace Imi\Model;

use Imi\Util\Imi;
use Imi\RequestContext;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Model\Key\KeyRule;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\Annotation\Serializables;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\ExtractProperty;
use Imi\Model\Annotation\Column;

abstract class ModelManager
{
    /**
     * 驼峰命名缓存
     * @var array
     */
    private static $isCamelCache = [];

    /**
     * 键规则缓存
     * @var array
     */
    private static $keyRules = [];

    /**
     * 字段缓存
     *
     * @var array
     */
    private static $fields = [];

    /**
     * 获取当前模型类的类注解
     * @param string|object $object
     * @param string $annotationClass
     * @return \Imi\Bean\Annotation\Base|null
     */
    public static function getAnnotation($object, $annotationClass)
    {
        return AnnotationManager::getClassAnnotations(BeanFactory::getObjectClass($object), $annotationClass)[0] ?? null;
    }

    /**
     * 获取当前模型类的属性注解
     * @param string|object $object
     * @param string $propertyName
     * @param string $annotationClass
     * @return \Imi\Bean\Annotation\Base|null
     */
    public static function getPropertyAnnotation($object, $propertyName, $annotationClass)
    {
        return AnnotationManager::getPropertyAnnotations(BeanFactory::getObjectClass($object), $propertyName, $annotationClass)[0] ?? null;
    }

    /**
     * 获取当前模型类的表名
     * @param string|object $object
     * @return string
     */
    public static function getTable($object)
    {
        $tableAnnotation = static::getAnnotation($object, Table::class);
        if(null !== $tableAnnotation)
        {
            return $tableAnnotation->name;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取当前模型类数据库连接池名
     * @param string|object $object
     * @return string
     */
    public static function getDbPoolName($object)
    {
        $tableAnnotation = static::getAnnotation($object, Table::class);
        if(null !== $tableAnnotation)
        {
            return $tableAnnotation->dbPoolName;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取当前模型主键
     * 如果是联合主键返回数组，否则为字符串
     * @param string|object $object
     * @return string|string[]|null
     */
    public static function getId($object)
    {
        $tableAnnotation = static::getAnnotation($object, Table::class);
        if(null !== $tableAnnotation)
        {
            return $tableAnnotation->id;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取第一个主键
     *
     * @param string|object $object
     * @return string|null
     */
    public static function getFirstId($object)
    {
        $tableAnnotation = static::getAnnotation($object, Table::class);
        if(null !== $tableAnnotation)
        {
            if(is_array($tableAnnotation->id))
            {
                return reset($tableAnnotation->id);
            }
            else
            {
                return $tableAnnotation->id;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取当前模型字段配置
     * @param string|object $object
     * @return \Imi\Model\Annotation\Column[]
     */
    public static function getFields($object)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        if(!isset(static::$fields[$objectClass]))
        {
            $annotationsSet = AnnotationManager::getPropertiesAnnotations($objectClass, Column::class);
            $fields = [];
            foreach($annotationsSet as $propertyName => $annotations)
            {
                $annotation = $annotations[0];
                $fields[$annotation->name ?? $propertyName] = $annotation;
            }
            static::$fields[$objectClass] = $fields;
        }
        return static::$fields[$objectClass];
    }

    /**
     * 获取当前模型字段名数组
     * @param string|object $object
     * @return string[]
     */
    public static function getFieldNames($object)
    {
        return array_keys(static::getFields($object));
    }

    /**
     * 模型是否为驼峰命名
     * @param  string|object
     * @return boolean
     */
    public static function isCamel($object)
    {
        $class = BeanFactory::getObjectClass($object);
        if(!isset(static::$isCamelCache[$class]))
        {
            static::$isCamelCache[$class] = static::getAnnotation($object, Entity::class)->camel ?? true;
        }
        return static::$isCamelCache[$class];
    }

    /**
     * 获取键
     * @param string|object $object
     * @return \Imi\Model\Key\KeyRule
     */
    public static function getKeyRule($object)
    {
        $class = BeanFactory::getObjectClass($object);
        if(!isset(static::$keyRules[$class]))
        {
            $key = static::getAnnotation($object, RedisEntity::class)->key;
            preg_match_all('/{([^}]+)}/', $key, $matches);
            static::$keyRules[$class] = new KeyRule($key, $matches[1]);
        }
        return static::$keyRules[$class];
    }
    
    /**
     * 获取当前模型类的Redis注解
     * @param string|object $object
     * @return \Imi\Model\Annotation\RedisEntity|null
     */
    public static function getRedisEntity($object)
    {
        $annotation = static::getAnnotation($object, RedisEntity::class);
        if(null !== $annotation)
        {
            return $annotation;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取模型类的批量设置序列化注解
     *
     * @param string|object $object
     * @return \Imi\Model\Annotation\Serializables
     */
    public static function getSerializables($object)
    {
        $annotation = static::getAnnotation($object, Serializables::class);
        if(null !== $annotation)
        {
            return $annotation;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取模型类的提取属性注解
     *
     * @param string|object $object
     * @return array
     */
    public static function getExtractPropertys($object)
    {
        $list = AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($object), ExtractProperty::class);
        if(null !== $list)
        {
            return $list;
        }
        else
        {
            return [];
        }
    }
}