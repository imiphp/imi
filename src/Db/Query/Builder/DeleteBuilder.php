<?php
namespace Imi\Db\Query\Builder;

class DeleteBuilder extends BaseBuilder
{
	public function build(...$args)
	{
		$option = $this->query->getOption();

		$sql = 'delete from ' . $option->table
				. $this->parseWhere($option->where)
				. $this->parseOrder($option->order)
				. $this->parseLimit($option->offset, $option->limit)
				;

		
		$this->query->bindValues($this->params);

		return $sql;
	}
}