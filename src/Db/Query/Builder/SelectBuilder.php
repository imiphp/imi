<?php
namespace Imi\Db\Query\Builder;

class SelectBuilder extends BaseBuilder
{
	/**
	 * 生成SQL语句
	 * @return string
	 */
	public function build(...$args)
	{
		$option = $this->query->getOption();
		$sql = 'select ' . $this->parseDistinct($option->distinct)
				. $this->parseField($option->field)
				. ' from '
				. $option->table
				. $this->parseJoin($option->join)
				. $this->parseWhere($option->where)
				. $this->parseGroup($option->group)
				. $this->parseHaving($option->having)
				. $this->parseOrder($option->order)
				. $this->parseLimit($option->offset, $option->limit)
				;
		$this->query->bindValues($this->params);
		return $sql;
	}
}