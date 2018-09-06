<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Db\Query\Where\Where;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\OneToMany;


abstract class Update
{
	/**
	 * 处理更新
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public static function parse($model, $propertyName, $annotation)
	{
		if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
		{
			static::parseByOneToOne($model, $propertyName, $annotation);
		}
		else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
		{
			static::parseByOneToMany($model, $propertyName, $annotation);
		}
	}

	/**
	 * 处理一对一更新
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
	 * @return void
	 */
	public static function parseByOneToOne($model, $propertyName, $annotation)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		$autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
		$autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');

		if($autoUpdate)
		{
			if(!$autoUpdate->status)
			{
				return;
			}
		}
		else if(!$autoSave || !$autoSave->status)
		{
			return;
		}

		$struct = new OneToOne($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();

		$model->$propertyName->$rightField = $model->$leftField;
		$model->$propertyName->update();
	}

	/**
	 * 处理一对多更新
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
	 * @return void
	 */
	public static function parseByOneToMany($model, $propertyName, $annotation)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		$autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
		$autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');

		if($autoUpdate)
		{
			if(!$autoUpdate->status)
			{
				return;
			}
		}
		else if(!$autoSave || !$autoSave->status)
		{
			return;
		}

		$struct = new OneToMany($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();
		$rightModel = $struct->getRightModel();

		// 是否删除无关数据
		if($autoUpdate)
		{
			$orphanRemoval = $autoUpdate->orphanRemoval;
		}
		else if($autoSave)
		{
			$orphanRemoval = $autoSave->orphanRemoval;
		}
		else
		{
			$orphanRemoval = false;
		}

		if($orphanRemoval)
		{
			// 删除无关联数据
			$pks = ModelManager::getId($rightModel);
			if(is_array($pks))
			{
				if(isset($pks[1]))
				{
					throw new \RuntimeException(sprintf('%s can not OneToMany, because has more than 1 primary keys', $rightModel));
				}
				$pk = $pks[0];
			}
			else
			{
				$pk = $pks;
			}

			$oldIDs = $rightModel::query()->where($rightField, '=', $model->$leftField)->field($pk)->select()->getColumn();

			$updateIDs = [];
			foreach($model->$propertyName as $row)
			{
				if(null !== $row->$pk)
				{
					$updateIDs[] = $row->$pk;
				}
				$row->$rightField = $model->$leftField;
				$row->save();
			}

			$deleteIDs = array_diff($oldIDs, $updateIDs);

			if(isset($deleteIDs[0]))
			{
				// 批量删除
				$rightModel::deleteBatch(function(IQuery $query) use($pk, $deleteIDs){
					$query->whereIn($pk, $deleteIDs);
				});
			}
		}
		else
		{
			// 直接更新
			foreach($model->$propertyName as $row)
			{
				$row->$rightField = $model->$leftField;
				$row->save();
			}
		}
	}

	public static function hasUpdateRelation($className, $propertyName = null)
	{
		$relationParser = RelationParser::getInstance();
		$relations = $relationParser->getRelations($className);
		if(null === $relations)
		{
			return false;
		}

		if(null === $propertyName)
		{
			foreach($relations as $name => $annotation)
			{
				$autoUpdate = $relationParser->getPropertyAnnotation($className, $name, 'AutoUpdate');
				$autoSave = $relationParser->getPropertyAnnotation($className, $name, 'AutoSave');
		
				if($autoUpdate)
				{
					if(!$autoUpdate->status)
					{
						continue;
					}
				}
				else if(!$autoSave || !$autoSave->status)
				{
					continue;
				}
			}
		}
		else
		{
			$autoUpdate = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoUpdate');
			$autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');
	
			if($autoUpdate)
			{
				if(!$autoUpdate->status)
				{
					return false;
				}
			}
			else if(!$autoSave || !$autoSave->status)
			{
				return false;
			}
			return true;
		}
		return false;
	}

}