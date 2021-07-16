<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Interfaces\IJoin;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Join implements IJoin
{
    use TRaw;

    /**
     * 表名.
     */
    protected Table $table;

    /**
     * 在 join b on a.id=b.id 中的 a.id.
     */
    protected string $left = '';

    /**
     * 在 join b on a.id=b.id 中的 =.
     */
    protected string $operation = '';

    /**
     * join b on a.id=b.id 中的 b.id.
     */
    protected string $right = '';

    /**
     * where条件.
     */
    protected ?IBaseWhere $where = null;

    /**
     * join类型，默认inner.
     */
    protected string $type = 'inner';

    public function __construct(IQuery $query, ?string $table = null, ?string $left = null, ?string $operation = null, ?string $right = null, ?string $tableAlias = null, ?IBaseWhere $where = null, string $type = 'inner')
    {
        $this->table = $thisTable = new Table();
        $thisTable->setValue($table, $query);
        if (null !== $tableAlias)
        {
            $thisTable->setAlias($tableAlias);
        }
        $this->left = $left;
        $this->operation = $operation;
        $this->right = $right;
        $this->where = $where;
        $this->type = $type;
    }

    /**
     * 表名.
     */
    public function getTable(IQuery $query): ?string
    {
        return $this->table->toString($query);
    }

    /**
     * 在 join b on a.id=b.id 中的 a.id.
     */
    public function getLeft(): ?string
    {
        return $this->left;
    }

    /**
     * 在 join b on a.id=b.id 中的 =.
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * join b on a.id=b.id 中的 b.id.
     */
    public function getRight(): ?string
    {
        return $this->right;
    }

    /**
     * 表别名.
     */
    public function getTableAlias(): ?string
    {
        return $this->table->getAlias();
    }

    /**
     * where条件.
     */
    public function getWhere(): ?IBaseWhere
    {
        return $this->where;
    }

    /**
     * join类型，默认inner.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * 设置表名.
     */
    public function setTable(string $table, IQuery $query): void
    {
        $this->table = $thisTable = new Table();
        $thisTable->setValue($table, $query);
    }

    /**
     * 设置在 join b on a.id=b.id 中的 a.id.
     */
    public function setLeft(?string $left): void
    {
        $this->left = $left;
    }

    /**
     * 设置在 join b on a.id=b.id 中的 =.
     */
    public function setOperation(?string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * 设置join b on a.id=b.id 中的 b.id.
     */
    public function setRight(?string $right): void
    {
        $this->right = $right;
    }

    /**
     * 设置表别名.
     */
    public function setTableAlias(?string $tableAlias): void
    {
        $this->table->setAlias($tableAlias);
    }

    /**
     * 设置where条件.
     */
    public function setWhere(?IBaseWhere $where): void
    {
        $this->where = $where;
    }

    /**
     * 设置join类型.
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        $result = $this->type . ' join ' . $this->table->toString($query) . ' on ' . $query->fieldQuote($this->left) . $this->operation . $query->fieldQuote($this->right);
        if ($this->where instanceof IBaseWhere)
        {
            $result .= ' ' . $this->where->toString($query);
        }

        return $result;
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        if ($this->where instanceof IBaseWhere)
        {
            return $this->where->getBinds();
        }
        else
        {
            return [];
        }
    }
}
