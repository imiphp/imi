<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Mysql\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWhereBrackets;
use Imi\Db\Query\Traits\TRaw;

class WhereBrackets extends BaseWhere implements IWhereBrackets
{
    use TRaw;

    /**
     * 回调.
     *
     * @var callable
     */
    protected $callback;

    /**
     * 绑定的数据们.
     */
    protected array $binds = [];

    public function __construct(callable $callback = null, string $logicalOperator = LogicalOperator::AND)
    {
        $this->callback = $callback;
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * {@inheritDoc}
     */
    public function getLogicalOperator(): string
    {
        return $this->logicalOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function setLogicalOperator(string $logicalOperator): void
    {
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function toStringWithoutLogic(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }
        $binds = &$this->binds;
        $callResult = ($this->callback)();
        if (\is_array($callResult))
        {
            if (empty($callResult))
            {
                return '';
            }

            $result = '(';
            foreach ($callResult as $i => $callResultItem)
            {
                if ($callResultItem instanceof IBaseWhere)
                {
                    if (0 === $i)
                    {
                        $result .= $callResultItem->toStringWithoutLogic($query) . ' ';
                    }
                    else
                    {
                        $result .= $callResultItem->getLogicalOperator() . ' ' . $callResultItem->toStringWithoutLogic($query) . ' ';
                    }
                    $binds = array_merge($binds, $callResultItem->getBinds());
                }
                else
                {
                    $result .= $callResultItem . ' ';
                }
            }

            return $result . ')';
        }
        elseif ($callResult instanceof IBaseWhere)
        {
            $result = $callResult->toStringWithoutLogic($query);
            $binds = array_merge($binds, $callResult->getBinds());

            return $result;
        }
        else
        {
            return (string) $callResult;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
}
