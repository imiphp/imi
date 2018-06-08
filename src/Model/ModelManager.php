<?php
namespace Imi\Model;

use Imi\Util\Imi;
use Imi\Model\Parser\ModelParser;
use Imi\RequestContext;

abstract class ModelManager
{
	/**
	 * 获取当前模型类的类注解
	 * @param string|object $object
	 * @param string $annotationClass
	 * @return \Imi\Bean\Annotation\Base|null
	 */
	public static function getAnnotation($object, $annotationClass)
	{
		$option = ModelParser::getInstance()->getData()[static::getObjectClass($object)] ?? [];
		$key = Imi::getClassShortName($annotationClass);
		return $option[$key] ?? null;
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
		$option = ModelParser::getInstance()->getData()[static::getObjectClass($object)] ?? [];
		$key = Imi::getClassShortName($annotationClass);
		return $option['properties'][$propertyName][$key] ?? null;
	}

	/**
	 * 获取当前模型类的表名
	 * @param string|object $object
	 * @return string
	 */
	public static function getTable($object)
	{
		$tableAnnotation = static::getAnnotation($object, 'Table');
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
		$tableAnnotation = static::getAnnotation($object, 'Table');
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
	 * @return string|string[]
	 */
	public static function getId($object)
	{
		$tableAnnotation = static::getAnnotation($object, 'Table');
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
	 * 获取当前模型字段配置
	 * @param string|object $object
	 * @return \Imi\Model\Annotation\Column[]
	 */
	public static function getFields($object)
	{
		$objectClass = static::getObjectClass($object);
		$key = 'Model.fields.' . $objectClass;
		$fields = RequestContext::get($key);
		if(null === $fields)
		{
			$option = ModelParser::getInstance()->getData()[$objectClass] ?? [];
			$fields = [];
			foreach($option['properties'] as $name => $item)
			{
				$fields[$name] = $item['Column'];
			}
			RequestContext::set($key, $fields);
		}
		return $fields;
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
	 * 获取对象类名
	 * @param string|object $object
	 * @return string
	 */
	private static function getObjectClass($object)
	{
		if(is_object($object))
		{
			return get_class($object);
		}
		else
		{
			return (string)$object;
		}
	}
}