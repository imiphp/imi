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
     */
    private static array $annotation = [];

    /**
     * 模型类属性注解缓存.
     */
    private static array $propertyAnnotation = [];

    /**
     * 驼峰命名缓存.
     */
    private static array $isCamelCache = [];

    /**
     * 键规则缓存.
     */
    private static array $keyRules = [];

    /**
     * member规则缓存.
     */
    private static array $memberRules = [];

    /**
     * 字段缓存.
     */
    private static array $fields = [];

    /**
     * 表名缓存.
     */
    private static array $table = [];

    /**
     * 数据库连接池名缓存.
     */
    private static array $dbPoolName = [];

    /**
     * 主键缓存.
     */
    private static array $id = [];

    /**
     * 模型类的提取属性注解缓存.
     */
    private static array $extractPropertys = [];

    private function __construct()
    {
    }

    /**
     * 获取当前模型类的类注解.
     *
     * @param string|object $object
     */
    public static function getAnnotation($object, string $annotationClass): ?Base
    {
        $objectClass = BeanFactory::getObjectClass($object);

        return self::$annotation[$objectClass][$annotationClass]
            ?? (self::$annotation[$objectClass][$annotationClass] = AnnotationManager::getClassAnnotations($objectClass, $annotationClass)[0] ?? null);
    }

    /**
     * 获取当前模型类的属性注解.
     *
     * @param string|object $object
     */
    public static function getPropertyAnnotation($object, string $propertyName, string $annotationClass): ?Base
    {
        $objectClass = BeanFactory::getObjectClass($object);
        if (!isset(self::$propertyAnnotation[$objectClass][$propertyName][$annotationClass]))
        {
            self::$propertyAnnotation[$objectClass][$propertyName][$annotationClass] = AnnotationManager::getPropertyAnnotations(BeanFactory::getObjectClass($object), $propertyName, $annotationClass)[0] ?? null;
        }

        return self::$propertyAnnotation[$objectClass][$propertyName][$annotationClass];
    }

    /**
     * 获取当前模型类的表名.
     *
     * @param string|object $object
     */
    public static function getTable($object): string
    {
        $objectClass = BeanFactory::getObjectClass($object);
        if (!isset(self::$table[$objectClass]))
        {
            /** @var Table|null $tableAnnotation */
            $tableAnnotation = static::getAnnotation($object, Table::class);

            self::$table[$objectClass] = $tableAnnotation ? $tableAnnotation->name : null;
        }

        return self::$table[$objectClass];
    }

    /**
     * 获取当前模型类数据库连接池名.
     *
     * @param string|object $object
     */
    public static function getDbPoolName($object): string
    {
        $objectClass = BeanFactory::getObjectClass($object);
        if (!isset(self::$dbPoolName[$objectClass]))
        {
            /** @var Table|null $tableAnnotation */
            $tableAnnotation = static::getAnnotation($object, Table::class);

            self::$dbPoolName[$objectClass] = $tableAnnotation ? $tableAnnotation->dbPoolName : null;
        }

        return self::$dbPoolName[$objectClass];
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
        if (!isset(self::$id[$objectClass]))
        {
            /** @var Table|null $tableAnnotation */
            $tableAnnotation = static::getAnnotation($object, Table::class);

            self::$id[$objectClass] = $tableAnnotation ? $tableAnnotation->id : null;
        }

        return self::$id[$objectClass];
    }

    /**
     * 获取第一个主键.
     *
     * @param string|object $object
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
        if (!isset(self::$fields[$objectClass]))
        {
            $annotationsSet = AnnotationManager::getPropertiesAnnotations($objectClass, Column::class);
            $fields = [];
            foreach ($annotationsSet as $propertyName => $annotations)
            {
                $annotation = $annotations[0];
                $fields[$annotation->name ?? $propertyName] = $annotation;
            }

            self::$fields[$objectClass] = $fields;
        }

        return self::$fields[$objectClass];
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
     */
    public static function isCamel($object): bool
    {
        $class = BeanFactory::getObjectClass($object);
        if (!isset(self::$isCamelCache[$class]))
        {
            /** @var Entity|null $entity */
            $entity = static::getAnnotation($object, Entity::class);

            self::$isCamelCache[$class] = $entity ? $entity->camel : true;
        }

        return self::$isCamelCache[$class];
    }

    /**
     * 获取键.
     *
     * @param string|object $object
     */
    public static function getKeyRule($object): KeyRule
    {
        $class = BeanFactory::getObjectClass($object);
        if (!isset(self::$keyRules[$class]))
        {
            /** @var RedisEntity|null $redisEntity */
            $redisEntity = static::getAnnotation($object, RedisEntity::class);
            $key = $redisEntity ? $redisEntity->key : '';
            preg_match_all('/{([^}]+)}/', $key, $matches);

            self::$keyRules[$class] = new KeyRule($key, $matches[1]);
        }

        return self::$keyRules[$class];
    }

    /**
     * 获取Member.
     *
     * @param string|object $object
     */
    public static function getMemberRule($object): KeyRule
    {
        $class = BeanFactory::getObjectClass($object);
        if (!isset(self::$memberRules[$class]))
        {
            /** @var RedisEntity|null $redisEntity */
            $redisEntity = static::getAnnotation($object, RedisEntity::class);
            $key = $redisEntity ? $redisEntity->member : '';
            preg_match_all('/{([^}]+)}/', $key, $matches);

            self::$memberRules[$class] = new KeyRule($key, $matches[1]);
        }

        return self::$memberRules[$class];
    }

    /**
     * 获取当前模型类的Redis注解.
     *
     * @param string|object $object
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
        if (!isset(self::$extractPropertys[$class]))
        {
            self::$extractPropertys[$class] = AnnotationManager::getPropertiesAnnotations(BeanFactory::getObjectClass($object), ExtractProperty::class);
        }

        return self::$extractPropertys[$class];
    }
}
