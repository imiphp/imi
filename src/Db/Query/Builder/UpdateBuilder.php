<?php
namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Query;

class UpdateBuilder extends BaseBuilder
{
	public function build(...$args)
	{
		$option = $this->query->getOption();
		list($data) = $args;
		$valueParams = [];
		$sql = 'update ' . $option->table . ' set ';

		// set后面的field=value
		$setStrs = [];
		foreach($data as $k => $v)
		{
			$valueParam = Query::getAutoParamName();
			$valueParams[] = $valueParam;
			$this->params[$valueParam] = $v;
			$setStrs[] = $this->parseKeyword($k) . ' = ' . $valueParam;
		}

		$sql .= implode(',', $setStrs)
			. $this->parseWhere($option->where)
			. $this->parseOrder($option->order)
			. $this->parseLimit($option->offset, $option->limit);

		$this->query->bindValues($this->params);
		return $sql;
	}
}