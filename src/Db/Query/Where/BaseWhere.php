<?php
namespace Imi\Db\Query\Where;

class BaseWhere
{
	public function __toString()
	{
		return $this->logicalOperator . ' ' . $this->toStringWithoutLogic();
	}

}