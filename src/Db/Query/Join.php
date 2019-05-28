<?php
namespace Imi\Db\Query;

use Imi\Db\Query\Traits\TRaw;
use Imi\Db\Query\Interfaces\IJoin;
use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Interfaces\IWhere;

class Join implements IJoin
{
    use TRaw;
    use TKeyword;

    /**
     * 表名
     * @var string
     */
    protected $table;

    /**
     * 在 join b on a.id=b.id 中的 a.id
     * @var string
     */
    protected $left;

    /**
     * 在 join b on a.id=b.id 中的 =
     * @var string
     */
    protected $operation;

    /**
     * join b on a.id=b.id 中的 b.id
     * @var string
     */
    protected $right;

    /**
     * 表别名
     * @var string
     */
    protected $tableAlias = null;

    /**
     * where条件
     * @var \Imi\Db\Query\Interfaces\IBaseWhere
     */
    protected $where = null;

    /**
     * join类型，默认inner
     * @var string
     */
    protected $type = 'inner';

    public function __construct(string $table = null, string $left = null, string $operation = null, string $right = null, string $tableAlias = null, IBaseWhere $where = null, string $type = 'inner')
    {
        $this->table = $table;
        $this->left = $left;
        $this->operation = $operation;
        $this->right = $right;
        $this->tableAlias = $tableAlias;
        $this->where = $where;
        $this->type = $type;
    }
    
    /**
     * 表名
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * 在 join b on a.id=b.id 中的 a.id
     * @return string
     */
    public function getLeft(): string
    {
        return $this->left;
    }

    /**
     * 在 join b on a.id=b.id 中的 =
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * join b on a.id=b.id 中的 b.id
     * @return string
     */
    public function getRight(): string
    {
        return $this->right;
    }

    /**
     * 表别名
     * @return string
     */
    public function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    /**
     * where条件
     * @return \Imi\Db\Query\Interfaces\IBaseWhere
     */
    public function getWhere(): IBaseWhere
    {
        return $this->where;
    }

    /**
     * join类型，默认inner
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * 设置表名
     * @param string $table
     * @return void
     */
    public function setTable(string $table = null)
    {
        $this->table = $table;
    }

    /**
     * 设置在 join b on a.id=b.id 中的 a.id
     * @param string $left
     * @return void
     */
    public function setLeft(string $left)
    {
        $this->left = $left;
    }

    /**
     * 设置在 join b on a.id=b.id 中的 =
     * @param string $operation
     * @return void
     */
    public function setOperation(string $operation)
    {
        $this->operation = $operation;
    }

    /**
     * 设置join b on a.id=b.id 中的 b.id
     * @param string $right
     * @return void
     */
    public function setRight(string $right)
    {
        $this->right = $right;
    }

    /**
     * 设置表别名
     * @param string $tableAlias
     * @return void
     */
    public function setTableAlias(string $tableAlias)
    {
        $this->tableAlias = $tableAlias;
    }

    /**
     * 设置where条件
     * @param IBaseWhere $where
     * @return void
     */
    public function setWhere(IBaseWhere $where)
    {
        $this->where = $where;
    }

    /**
     * 设置join类型
     * @param string $type
     * @return void
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
    
    public function __toString()
    {
        if($this->isRaw)
        {
            return $this->rawSQL;
        }
        $result = $this->type . ' join ' . $this->parseKeyword($this->table . ' ' . $this->tableAlias) . ' on ' . $this->parseKeyword($this->left) . $this->operation . $this->parseKeyword($this->right);
        if($this->where instanceof IBaseWhere)
        {
            $result .= ' ' . $this->where;
        }
        return $result;
    }

    /**
     * 获取绑定的数据们
     * @return array
     */
    public function getBinds()
    {
        if($this->where instanceof IBaseWhere)
        {
            return $this->where->getBinds();
        }
        else
        {
            return [];
        }
    }
}