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
    protected ?Table $table = null;

    /**
     * 在 join b on a.id=b.id 中的 a.id.
     */
    protected ?string $left = null;

    /**
     * 在 join b on a.id=b.id 中的 =.
     */
    protected ?string $operation = null;

    /**
     * join b on a.id=b.id 中的 b.id.
     */
    protected ?string $right = null;

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
        if (null !== $table)
        {
            $thisTable->setValue($table, $query);
        }
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
     * {@inheritDoc}
     */
    public function getTable(IQuery $query): ?string
    {
        return $this->table->toString($query);
    }

    /**
     * {@inheritDoc}
     */
    public function getLeft(): ?string
    {
        return $this->left;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * {@inheritDoc}
     */
    public function getRight(): ?string
    {
        return $this->right;
    }

    /**
     * {@inheritDoc}
     */
    public function getTableAlias(): ?string
    {
        return $this->table->getAlias();
    }

    /**
     * {@inheritDoc}
     */
    public function getWhere(): ?IBaseWhere
    {
        return $this->where;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function setTable(string $table, IQuery $query): void
    {
        $this->table = $thisTable = new Table();
        $thisTable->setValue($table, $query);
    }

    /**
     * {@inheritDoc}
     */
    public function setLeft(?string $left): void
    {
        $this->left = $left;
    }

    /**
     * {@inheritDoc}
     */
    public function setOperation(?string $operation): void
    {
        $this->operation = $operation;
    }

    /**
     * {@inheritDoc}
     */
    public function setRight(?string $right): void
    {
        $this->right = $right;
    }

    /**
     * {@inheritDoc}
     */
    public function setTableAlias(?string $tableAlias): void
    {
        $this->table->setAlias($tableAlias);
    }

    /**
     * {@inheritDoc}
     */
    public function setWhere(?IBaseWhere $where): void
    {
        $this->where = $where;
    }

    /**
     * {@inheritDoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        $result = $this->type . ' join ' . $this->table->toString($query) . ' on ' . $query->fieldQuote($this->left ?? '') . $this->operation . $query->fieldQuote($this->right ?? '');
        if ($this->where instanceof IBaseWhere)
        {
            $result .= ' ' . $this->where->toString($query);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
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
