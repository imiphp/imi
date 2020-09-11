<?php

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\ExtractProperty;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Table;
use Imi\Model\Key\KeyRule;

abstract class ModelManager
{
    /**
     * 模型类注解缓存.
     *
     * @var array
     */
    private static $annotation = [];

    /**
     * 模型类属性注解缓存.
     *
     * @var array
     */
    private static $propertyAnnotation = [];

    /**
     * 驼峰命名缓存.
     *
     * @var array
     */
    private static $isCamelCache = [];

    /**
     * 键规则缓存.
     *
     * @var array
     */
    private static $keyRules = [];

    /**
     * member规则缓存.
     *
     * @var array
     */
    private static $memberRules = [];

    /**
     * 字段缓存.
     *
     * @var array
     */
    private static $fields = [];

    /**
     * 表名缓存.
     *
     * @var array
     */
    private static $table = [];

    /**
     * 数据库连接池名缓存.
     *
     * @var array
     */
    private static $dbPoolName = [];

    /**
     * 主键缓存.
     *
     * @var array
     */
    private static $id = [];

    /**
     * 模型类的提取属性注解缓存.
     *
     * @var array
     */
    private static $extractPropertys;

    /**
     * 获取当前模型类的类注解.
     *
     * @param string|object $object
     * @param string        $annotationClass
     *
     * @return \Imi\Bean\Annotation\Base|null
     */
    public static function getAnnotation($object, $annotationClass)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticAnnotation = &static::$annotation;
        if (isset($staticAnnotation[$objectClass][$annotationClass]))
        {
            return $staticAnnotation[$objectClass][$annotationClass];
        }
        else
        {
            return $staticAnnotation[$objectClass][$annotationClass] = AnnotationManager::getClassAnnotations($objectClass, $annotationClass)[0] ?? null;
        }
    }

    /**
     * 获取当前模型类的属性注解.
     *
     * @param string|object $object
     * @param string        $propertyName
     * @param string        $annotationClass
     *
     * @return \Imi\Bean\Annotation\Base|null
     */
    public static function getPropertyAnnotation($object, $propertyName, $annotationClass)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticPropertyAnnotation = &static::$propertyAnnotation;
        if (isset($staticPropertyAnnotation[$objectClass][$propertyName][$annotationClass]))
        {
            return $staticPropertyAnnotation[$objectClass][$propertyName][$annotationClass];
        }
        else
        {
            return $staticPropertyAnnotation[$objectClass][$propertyName][$annotationClass] = AnnotationManager::getPropertyAnnotations(BeanFactory::getObjectClass($object), $propertyName, $annotationClass)[0] ?? null;
        }
    }

    /**
     * 获取当前模型类的表名.
     *
     * @param string|object $object
     *
     * @return string
     */
    public static function getTable($object)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticTable = &static::$table;
        if (isset($staticTable[$objectClass]))
        {
            return $staticTable[$objectClass];
        }
        else
        {
            $tableAnnotation = static::getAnnotation($object, Table::class);

            return $staticTable[$objectClass] = $tableAnnotation ? $tableAnnotation->name : null;
        }
    }

    /**
     * 获取当前模型类数据库连接池名.
     *
     * @param string|object $object
     *
     * @return string
     */
    public static function getDbPoolName($object)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticDbPoolName = &static::$dbPoolName;
        if (isset($staticDbPoolName[$objectClass]))
        {
            return $staticDbPoolName[$objectClass];
        }
        else
        {
            $tableAnnotation = static::getAnnotation($object, Table::class);

            return $staticDbPoolName[$objectClass] = $tableAnnotation ? $tableAnnotation->dbPoolName : null;
        }
    }

    /**
     * 获取当前模型主键
     * 如果是联合主键返回数组，否则为字符串.
     *
     * @param string|object $object
     *
     * @return string|string[]|null
     */
    public static function getId($object)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticId = &static::$id;
        if (isset($staticId[$objectClass]))
        {
            return $staticId[$objectClass];
        }
        else
        {
            $tableAnnotation = static::getAnnotation($object, Table::class);

            return $staticId[$objectClass] = $tableAnnotation ? $tableAnnotation->id : null;
        }
    }

    /**
     * 获取第一个主键.
     *
     * @param string|object $object
     *
     * @return string|null
     */
    public static function getFirstId($object)
    {
        $id = self::getId($object);

        return $id[0] ?? null;
    }

    /**
     * 获取当前模型字段配置.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\Column[]
     */
    public static function getFields($object)
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticFields = &static::$fields;
        if (isset($staticFields[$objectClass]))
        {
            return $staticFields[$objectClass];
        }
        else
        {
            $annotationsSet = AnnotationManager::getPropertiesAnnotations($objectClass, Column::class);
            $fields = [];
            foreach ($annotationsSet as $propertyName => $annotations)
            {
                $annotation = $annotations[0];
                $fields[$annotation->name ?: $propertyName] = $annotation;
            }

            return $staticFields[$objectClass] = $fields;
        }
    }

    /**
     * 获取当前模型字段名数组.
     *
     * @param string|object $object
     *
     * @return string[]
     */
    public static function getFieldNames($object)
    {
        return array_keys(static::getFields($object));
    }

    /**
     * 模型是否为驼峰命名.
     *
     * @param  string|object
     *
     * @return bool
     */
    public static function isCamel($object)
    {
        $class = BeanFactory::getObjectClass($object);
        $staticIsCamelCache = &static::$isCamelCache;
        if (isset($staticIsCamelCache[$class]))
        {
            return $staticIsCamelCache[$class];
        }
        else
        {
            return $staticIsCamelCache[$class] = static::getAnnotation($object, Entity::class)->camel ?? true;
        }
    }

    /**
     * 获取键.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Key\KeyRule
     */
    public static function getKeyRule($object)
    {
        $class = BeanFactory::getObjectClass($object);
        $staticKeyRules = &static::$keyRules;
        if (isset($staticKeyRules[$class]))
        {
            return $staticKeyRules[$class];
        }
        else
        {
            $key = static::getAnnotation($object, RedisEntity::class)->key;
            preg_match_all('/{([^}]+)}/', $key, $matches);

            return $staticKeyRules[$class] = new KeyRule($key, $matches[1]);
        }
    }

    /**
     * 获取Member.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Key\KeyRule
     */
    public static function getMemberRule($object)
    {
        $class = BeanFactory::getObjectClass($object);
        $staticMemberRules = &static::$memberRules;
        if (isset($staticMemberRules[$class]))
        {
            return $staticMemberRules[$class];
        }
        else
        {
            $key = static::getAnnotation($object, RedisEntity::class)->member;
            preg_match_all('/{([^}]+)}/', $key, $matches);

            return $staticMemberRules[$class] = new KeyRule($key, $matches[1]);
        }
    }

    /**
     * 获取当前模型类的Redis注解.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\RedisEntity|null
     */
    public static function getRedisEntity($object)
    {
        return static::getAnnotation($object, RedisEntity::class);
    }

    /**
     * 获取模型类的批量设置序列化注解.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\Serializables
     */
    public static function getSerializables($object)
    {
        return static::getAnnotation($object, Serializables::class);
    }

    /**
     * 获取模型类的提取属性注解.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\ExtractProperty[]
     */
    public static function getExtractPropertys($object)
    {
        $class = BeanFactory::getObjectClass($object);
        $staticExtractPropertys = &static::$extractPropertys;
        if (isset($staticExtractPropertys[$class]))
        {
            return $staticExtractPropertys[$class];
        }
        else
        {
            return $staticExtractPropertys[$class] = AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($object), ExtractProperty::class);
        }
    }
}
