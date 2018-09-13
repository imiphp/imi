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
		if(null === $data)
		{
			$data = $this->query->getOption()->saveData;
		}
		if($data instanceof \Traversable)
		{
			$data = \iterator_to_array($data);
		}
		$valueParams = [];
		if(ArrayUtil::isAssoc($data))
		{
			$fields = [];
			// 键值数组
			foreach($data as $k => $v)
			{
				if($v instanceof \Imi\Db\Query\Raw)
				{
					if(!is_numeric($k))
					{
						$fields[] = $this->parseKeyword($k);
						$valueParams[] = (string)$v;
					}
				}
				else
				{
					$fields[] = $this->parseKeyword($k);
					$valueParam = Query::getAutoParamName();
					$valueParams[] = $valueParam;
					$this->params[$valueParam] = $v;
				}
			}
			$sql = 'insert into ' . $option->table . '(' . implode(',', $fields) . ') values(' . implode(',', $valueParams) . ')';
		}
		else
		{
			// 普通数组
			foreach($data as $v)
			{
				if($v instanceof \Imi\Db\Query\Raw)
				{
					$valueParams[] = (string)$v;
				}
				else
				{
					$valueParam = Query::getAutoParamName();
					$valueParams[] = $valueParam;
					$this->params[$valueParam] = $v;
				}
			}
			$sql = 'insert into ' . $option->table . ' values(' . implode(',', $valueParams) . ')';
		}
		$this->query->bindValues($this->params);
		return $sql;
	}
}