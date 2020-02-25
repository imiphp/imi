<?php
namespace Imi\Model\Tree;

use Imi\Model\ModelManager;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Util\ArrayUtil;

/**
 * 树形模型扩展
 */
trait TTreeModel
{
    /**
     * 获取 TreeModel 注解
     *
     * @return \Imi\Model\Tree\Annotation\TreeModel
     */
    private static function __getTreeModel()
    {
        return ModelManager::getAnnotation(static::__getRealClassName(), TreeModel::class);
    }

    /**
     * 获取主键字段名
     *
     * @return string
     */
    private static function __getIdField()
    {
        $treeModel = static::__getTreeModel();
        if($treeModel->idField)
        {
            return $treeModel->idField;
        }
        else
        {
            return static::__getMeta()->getFirstId();
        }
    }

    /**
     * 获取关联列表
     *
     * @param \Imi\Db\Query\Interfaces\IQuery $query
     * @return array
     */
    public static function getAssocList(IQuery $query = null)
    {
        if(null === $query)
        {
            $query = static::query();
        }
        $treeModel = ModelManager::getAnnotation(static::class, TreeModel::class);
        $idField = static::__getMeta()->getFirstId();
        return ArrayUtil::toTreeAssoc($query->select()->getArray(), $idField, $treeModel->parentField, $treeModel->childrenField);
    }

	/**
	 * 获取下属 N 级子节点的ID
     * 
     * 非递归实现，相比递归实现性能更高，更省内存
     * 
	 * @param int|null $parentId
     * @param bool $includeParentId 包含父级ID
	 * @param int $limitLevel 限制层级
	 * @param int $level 当前层数，内部参数不要手动传
	 * @return int[]
	 */
	public function getChildrenIds($parentId = null, $includeParentId = false, $limitLevel = -1)
	{
        $idField = static::__getIdField();
		if(is_array($parentId))
		{
			$ids = $parentId;
			if(!isset($ids[0]))
			{
				return [];
			}
		}
		else
		{
			$ids = [$parentId ?? $this[$idField]];
        }
        $idsList = [$ids];
        if($includeParentId)
        {
            $idsList2 = $idsList;
        }
        else
        {
            $idsList2 = [];
        }
        $level = 1;
        $parentField = static::__getTreeModel()->parentField;
        do {
            $i = null;
            foreach($idsList as $i => $ids)
            {
                unset($idsList[$i]);
                $tids = static::query()->field($idField)->whereIn($parentField, $ids)->select()->getColumn();
                if($tids)
                {
                    $idsList2[] = $idsList[] = $tids;
                }
            }
            if(null === $i)
            {
                break;
            }
            ++$level;
            if($limitLevel > 0 && $level >= $limitLevel)
            {
                break;
            }
        } while(true);
        return array_merge(...$idsList2);
    }

	/**
	 * 获取一级子节点的ID们
     * 
	 * @param int|null $parentId
	 * @return int[]
	 */
	public function getChildIds($parentId = null)
	{
        $idField = static::__getIdField();
		return static::query()->field($idField)->where(static::__getTreeModel()->parentField, '=', $parentId ?? $this[$idField])->select()->getColumn();
	}

    /**
     * 获取子成员对象列表，可以指定层级，默认无限级
     *
     * @param integer $parentId
     * @param integer $limitLevel
     * @return static[]
     */
    public function getChildrenList($parentId = null, $limitLevel = -1)
    {
        $ids = $this->getChildrenIds($parentId, false, $limitLevel);
        if(!$ids)
        {
            return [];
        }
        return static::query()->whereIn(static::__getIdField(), $ids)->select()->getArray();
    }

    /**
     * 获取父级对象
     *
     * @return static
     */
    public function getParent()
    {
        return static::find($this[static::__getTreeModel()->parentField]);
    }

    /**
     * 获取所有父级对象列表
     *
     * @return static[]
     */
    public function getParents()
    {
        $parents = [];
        $treeItem = $this;
        $parentField = static::__getTreeModel()->parentField;
        do {
            $treeItem = static::find($treeItem[$parentField]);
            if(!$treeItem)
            {
                break;
            }
            $parents[] = $treeItem;
        } while(true);
        return $parents;
    }

}
