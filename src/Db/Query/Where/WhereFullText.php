<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Query\Interfaces\IFullTextOptions;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWhereFullText;
use Imi\Db\Query\Traits\TRaw;

class WhereFullText extends BaseWhere implements IWhereFullText
{
    use TRaw;

    protected ?IFullTextOptions $options = null;

    public function __construct(?IFullTextOptions $options = null, string $logicalOperator = 'AND')
    {
        $this->options = $options;
        $this->logicalOperator = $logicalOperator;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): ?IFullTextOptions
    {
        return $this->options;
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
        if (!$this->options)
        {
            throw new \InvalidArgumentException('FullText options cannot be empty');
        }

        $result = $this->options->toWhereSql($query);
        $this->binds = $this->options->getBinds();

        return $result;
    }
}
