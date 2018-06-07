<?php
namespace Imi\Db\Query\Where;

use Imi\Db\Query\Traits\TRaw;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IWhere;
use Imi\Db\Query\Query;

class Where extends BaseWhere implements IWhere
{
	use TRaw;
	use TKeyword;

	/**
	 * 字段名
	 * @var string
	 */
	protected $fieldName;

	/**
	 * 比较符
	 * @var string
	 */
	protected $operation;

	/**
	 * 值
	 * @var mixed
	 */
	protected $value;

	/**
	 * 逻辑运算符
	 * @var string
	 */
	protected $logicalOperator;

	/**
	 * 绑定的数据们
	 * @var array
	 */
	protected $binds = [];

	public function __construct(string $fieldName = null, string $operation = null, $value = null, string $logicalOperator = LogicalOperator::AND)
	{
		$this->fieldName = $fieldName;
		$this->operation = $operation;
		$this->value = $value;
		$this->logicalOperator = $logicalOperator;
	}

	/**
	 * 字段名
	 * @return string
	 */
	public function getFieldName(): string
	{
		return $this->fieldName;
	}

	/**
	 * 比较符
	 * @return string
	 */
	public function getOperation(): string
	{
		return $this->operation;
	}

	/**
	 * 值
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * 逻辑运算符
	 * @return string
	 */
	public function getLogicalOperator(): string
	{
		return $this->logicalOperator;
	}

	/**
	 * 字段名
	 * @param string $fieldName
	 * @return void
	 */
	public function setFieldName(string $fieldName)
	{
		$this->fieldName = $fieldName;
	}

	/**
	 * 比较符
	 * @param string $operation
	 * @return void
	 */
	public function setOperation(string $operation)
	{
		$this->operation = $operation;
	}

	/**
	 * 值
	 * @param mixed $value
	 * @return void
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * 逻辑运算符
	 * @param string $logicalOperator
	 * @return void
	 */
	public function setLogicalOperator(string $logicalOperator)
	{
		$this->logicalOperator = $logicalOperator;
	}

	public function toStringWithoutLogic()
	{
		if($this->isRaw)
		{
			return $this->rawSQL;
		}
		$result = $this->parseKeyword($this->fieldName) . ' ' . $this->operation . ' ';
		switch($this->operation)
		{
			case 'between':
			case 'not between':
				$begin = Query::getAutoParamName();
				$end = Query::getAutoParamName();
				$result .= "{$begin} and {$end}";
				$this->binds[$begin] = $this->value[0];
				$this->binds[$end] = $this->value[1];
				break;
			default:
				$value = Query::getAutoParamName();
				$result .= $value;
				$this->binds[$value] = $this->value;
				break;
		}
		return $result;
	}

	/**
	 * 获取绑定的数据们
	 * @return array
	 */
	public function getBinds()
	{
		return $this->binds;
	}
}