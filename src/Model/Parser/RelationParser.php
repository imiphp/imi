<?php
namespace Imi\Model\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\OneToOne;


class RelationParser extends BaseParser
{
	/**
	 * 处理方法
	 * @param \Imi\Bean\Annotation\Base $annotation 注解类
	 * @param string $className 类名
	 * @param string $target 注解目标类型（类/属性/方法）
	 * @param string $targetName 注解目标名称
	 * @return void
	 */
	public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
	{
		if($annotation instanceof OneToOne)
		{
			$this->data[$className]['relations'][$targetName]['OneToOne'][] = $annotation;
		}
		else if($annotation instanceof JoinFrom)
		{
			$this->data[$className]['properties'][$targetName]['JoinFrom'] = $annotation;
		}
		else if($annotation instanceof JoinTo)
		{
			$this->data[$className]['properties'][$targetName]['JoinTo'] = $annotation;
		}
	}

	/**
	 * 获取关联关系
	 * ['propertyName'=>['OneToOne'=>[$annotation1, $annotation2...]]]
	 *
	 * @param string $className
	 * @return array
	 */
	public function getRelations($className)
	{
		return $this->data[$className]['relations'] ?? [];
	}

	/**
	 * 获取属性注解
	 *
	 * @param string $className
	 * @param string $propertyName
	 * @param string $annotationName
	 * @return \Imi\Bean\Annotation\Base|null
	 */
	public function getPropertyAnnotation($className, $propertyName, $annotationName)
	{
		return $this->data[$className]['properties'][$propertyName][$annotationName] ?? null;
	}

}