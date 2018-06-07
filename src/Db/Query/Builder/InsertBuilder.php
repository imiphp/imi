<?php
namespace Imi\Db\Query\Builder;

use Imi\Util\ArrayUtil;
use Imi\Db\Query\Query;


class InsertBuilder extends BaseBuilder
{
	public function build(...$args)
	{
		$option = $this->query->getOption();
		list($data) = $args;
		$valueParams = [];
		if(ArrayUtil::isAssoc($data))
		{
			$fields = [];
			// 键值数组
			foreach($data as $k => $v)
			{
				$fields[] = $this->parseKeyword($k);
				$valueParam = Query::getAutoParamName();
				$valueParams[] = $valueParam;
				$this->params[$valueParam] = $v;
			}
			$sql = 'insert into ' . $option->table . '(' . implode(',', $fields) . ') values(' . implode(',', $valueParams) . ')';
		}
		else
		{
			// 普通数组
			foreach($data as $v)
			{
				$valueParam = Query::getAutoParamName();
				$valueParams[] = $valueParam;
				$this->params[$valueParam] = $v;
			}
			$sql = 'insert into ' . $option->table . ' values(' . implode(',', $valueParams) . ')';
		}
		$this->query->bindValues($this->params);
		return $sql;
	}
}