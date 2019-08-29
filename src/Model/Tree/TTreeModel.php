<?php
namespace Imi\Model\Tree;

use Imi\Model\ModelManager;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Model\Tree\Annotation\TreeModel;

/**
 * 树形模型扩展
 */
trait TTreeModel
{
    /**
     * TreeModel 注解
     *
     * @var \Imi\Model\Tree\Annotation\TreeModel
     */
    private $treeModel;

    /**
     * 主键字段名
     *
     * @var string
     */
    private $idField;

    /**
     * 获取 TreeModel 注解
     *
     * @return \Imi\Model\Tree\Annotation\TreeModel
     */
    private function __getTreeModel()
    {
        if(null === $this->treeModel)
        {
            $this->treeModel = ModelManager::getAnnotation($this, TreeModel::class);
        }
        return $this->treeModel;
    }

    /**
     * 获取主键字段名
     *
     * @return string
     */
    private function __getIdField()
    {
        if(null === $this->idField)
        {
            $this->idField = ModelManager::getFirstId($this);
        }
        return $this->idField;
    }

    /**
     * 获取关联列表
     * 注意：每个成员都是数组，不再是模型对象
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
        $idField = ModelManager::getFirstId(static::class);
        // 查出所有记录
        $arr1 = $query->select()->getArray();
        $result = $arr2 = [];
        // 处理成ID为键名的数组
        foreach($arr1 as $item)
        {
            $arr2[$item[$idField]] = $item;
        }
        foreach($arr2 as $item)
        {
            if(isset($arr2[$item[$treeModel->parentField]]))
            {
                $arr2[$item[$treeModel->parentField]][$treeModel->childrenField][] = $arr2[$item[$idField]];
            }
            else
            {
                $result[] = $arr2[$item[$idField]];
            }
        }
        return $result;
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
        $idField = $this->__getIdField();
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
        $parentField = $this->__getTreeModel()->parentField;
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
        $idField = $this->__getIdField();
		return static::query()->field($idField)->where($this->__getTreeModel()->parentField, '=', $parentId ?? $this[$idField])->select()->getColumn();
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
        return static::query()->whereIn($this->__getIdField(), $ids)->select()->getArray();
    }

    /**
     * 获取父级对象
     *
     * @return static
     */
    public function getParent()
    {
        return static::find($this[$this->__getTreeModel()->parentField]);
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
        $parentField = $this->__getTreeModel()->parentField;
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
