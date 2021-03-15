<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Base;
use Imi\Bean\BeanFactory;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\ExtractProperty;
use Imi\Model\Annotation\RedisEntity;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Table;
use Imi\Model\Key\KeyRule;

class ModelManager
{
    /**
     * 模型类注解缓存.
     *
     * @var array
     */
    private static array $annotation = [];

    /**
     * 模型类属性注解缓存.
     *
     * @var array
     */
    private static array $propertyAnnotation = [];

    /**
     * 驼峰命名缓存.
     *
     * @var array
     */
    private static array $isCamelCache = [];

    /**
     * 键规则缓存.
     *
     * @var array
     */
    private static array $keyRules = [];

    /**
     * member规则缓存.
     *
     * @var array
     */
    private static array $memberRules = [];

    /**
     * 字段缓存.
     *
     * @var array
     */
    private static array $fields = [];

    /**
     * 表名缓存.
     *
     * @var array
     */
    private static array $table = [];

    /**
     * 数据库连接池名缓存.
     *
     * @var array
     */
    private static array $dbPoolName = [];

    /**
     * 主键缓存.
     *
     * @var array
     */
    private static array $id = [];

    /**
     * 模型类的提取属性注解缓存.
     *
     * @var array
     */
    private static array $extractPropertys = [];

    private function __construct()
    {
    }

    /**
     * 获取当前模型类的类注解.
     *
     * @param string|object $object
     * @param string        $annotationClass
     *
     * @return \Imi\Bean\Annotation\Base|null
     */
    public static function getAnnotation($object, string $annotationClass): ?Base
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
    public static function getPropertyAnnotation($object, string $propertyName, string $annotationClass): ?Base
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
    public static function getTable($object): string
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticTable = &static::$table;
        if (isset($staticTable[$objectClass]))
        {
            return $staticTable[$objectClass];
        }
        else
        {
            /** @var Table|null $tableAnnotation */
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
    public static function getDbPoolName($object): string
    {
        $objectClass = BeanFactory::getObjectClass($object);
        $staticDbPoolName = &static::$dbPoolName;
        if (isset($staticDbPoolName[$objectClass]))
        {
            return $staticDbPoolName[$objectClass];
        }
        else
        {
            /** @var Table|null $tableAnnotation */
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
            /** @var Table|null $tableAnnotation */
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
    public static function getFirstId($object): ?string
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
    public static function getFields($object): array
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
                $fields[$annotation->name ?? $propertyName] = $annotation;
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
    public static function getFieldNames($object): array
    {
        return array_keys(static::getFields($object));
    }

    /**
     * 模型是否为驼峰命名.
     *
     * @param string|object $object
     *
     * @return bool
     */
    public static function isCamel($object): bool
    {
        $class = BeanFactory::getObjectClass($object);
        $staticIsCamelCache = &static::$isCamelCache;
        if (isset($staticIsCamelCache[$class]))
        {
            return $staticIsCamelCache[$class];
        }
        else
        {
            /** @var Entity|null $entity */
            $entity = static::getAnnotation($object, Entity::class);

            return $staticIsCamelCache[$class] = $entity ? $entity->camel : true;
        }
    }

    /**
     * 获取键.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Key\KeyRule
     */
    public static function getKeyRule($object): KeyRule
    {
        $class = BeanFactory::getObjectClass($object);
        $staticKeyRules = &static::$keyRules;
        if (isset($staticKeyRules[$class]))
        {
            return $staticKeyRules[$class];
        }
        else
        {
            /** @var RedisEntity|null $redisEntity */
            $redisEntity = static::getAnnotation($object, RedisEntity::class);
            $key = $redisEntity ? $redisEntity->key : '';
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
    public static function getMemberRule($object): KeyRule
    {
        $class = BeanFactory::getObjectClass($object);
        $staticMemberRules = &static::$memberRules;
        if (isset($staticMemberRules[$class]))
        {
            return $staticMemberRules[$class];
        }
        else
        {
            /** @var RedisEntity|null $redisEntity */
            $redisEntity = static::getAnnotation($object, RedisEntity::class);
            $key = $redisEntity ? $redisEntity->member : '';
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
    public static function getRedisEntity($object): ?RedisEntity
    {
        // @phpstan-ignore-next-line
        return static::getAnnotation($object, RedisEntity::class);
    }

    /**
     * 获取模型类的批量设置序列化注解.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\Serializables|null
     */
    public static function getSerializables($object): ?Serializables
    {
        // @phpstan-ignore-next-line
        return static::getAnnotation($object, Serializables::class);
    }

    /**
     * 获取模型类的提取属性注解.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Annotation\ExtractProperty[][]
     */
    public static function getExtractPropertys($object): array
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
