<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\Pagination;

use Imi\Db\Query\Interfaces\IPaginateResult;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\PaginateResult;
use Imi\Db\Query\WrapField;
use Imi\Util\Pagination;

class BigTablePagination
{
    protected IQuery $query;

    protected string $idField = '';

    protected bool $cleanWhere = true;

    public function __construct(IQuery $query, string $idField = 'id', bool $cleanWhere = true)
    {
        $this->query = $query;
        $this->idField = $idField;
        $this->cleanWhere = $cleanWhere;
    }

    public function getQuery(): IQuery
    {
        return $this->query;
    }

    public function getIdField(): string
    {
        return $this->idField;
    }

    public function isCleanWhere(): bool
    {
        return $this->cleanWhere;
    }

    public function paginate(int $page, int $limit, array $options = []): IPaginateResult
    {
        if ($options['total'] ?? true)
        {
            $query = clone $this->query;
            $option = $query->getOption();
            $option->order = [];
            if ($option->distinct)
            {
                $option->field = [
                    new WrapField('count(distinct ', $option->field ?: ['*'], ')'),
                ];
                $total = (int) $query->select()->getScalar();
            }
            else
            {
                $total = (int) $query->count();
            }
        }
        else
        {
            $total = null;
        }
        $pagination = new Pagination($page, $limit);
        $query = clone $this->query;

        return new PaginateResult($this->select($page, $limit), $pagination->getLimitOffset(), $limit, $total, null === $total ? null : $pagination->calcPageCount($total), $options);
    }

    public function select(int $page, int $limit): IResult
    {
        $query = clone $this->query;
        $query->getOption()->field = [];
        $ids = $query->field($this->idField)
                     ->page($page, $limit)
                     ->select()
                     ->getColumn();

        $query = clone $this->query;
        $option = $query->getOption();
        $option->order = [];
        if ($this->cleanWhere)
        {
            $option->where = [];
        }

        if ($ids)
        {
            $valueNames = $bindValues = [];
            foreach ($ids as $i => $value)
            {
                $valueNames[] = $valueName = ':v' . $i;
                $bindValues[$valueName] = $value;
            }

            return $query->whereIn($this->idField, $ids)
                         ->orderRaw('field(' . $this->query->fieldQuote($this->idField) . ', ' . implode(',', $valueNames) . ')', $bindValues)
                         ->select();
        }
        else
        {
            return $query->whereRaw('1=2')
                         ->select();
        }
    }
}
