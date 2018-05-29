<?php
namespace Imi\Db\CoroutineMysql;

use Imi\Db\Interfaces\IStatement;
use Imi\Util\LazyArrayObject;

class Statement implements IStatement
{
	/**
	 * \Swoole\Coroutine\MySQL\Statement
	 * @var \Swoole\Coroutine\MySQL\Statement
	 */
	protected $statement;

	/**
	 * 数据
	 * @var array
	 */
	protected $data;

	/**
	 * bindColumn 关联关系
	 * @var array
	 */
	protected $columnBinds = [];

	/**
	 * bindParam和bindParam 关联关系
	 * @var array
	 */
	protected $binds = [];

	/**
	 * 当前游标位置
	 * @var int
	 */
	protected $cursor;

	/**
	 * 处理所有行数据处理器
	 * @var StatementFetchAllParser
	 */
	protected $fetchAllParser;

	public function __construct($statement, $data = null)
	{
		$this->statement = $statement;
		$this->data = $data;
		$this->cursor = null === $data ? -1 : 0;
		$this->fetchAllParser = new StatementFetchAllParser;
	}

	/**
	 * 绑定一列到一个 PHP 变量
	 * @param mixed $column
	 * @param mixed $param
	 * @param integer $type
	 * @param integer $maxLen
	 * @param mixed $driverData
	 * @return boolean
	 */
	public function bindColumn($column, &$param, int $type = null, int $maxLen = null, $driverData = null): bool
	{
		$this->columnBinds[$column] = [
			'param'		=>	&$param,
			'type'		=>	$type,
			'maxLen'	=>	$maxLen,
			'driverData'=>	$driverData
		];
		return true;
	}

	/**
	 * 绑定一个参数到指定的变量名
	 * @param mixed $parameter
	 * @param mixed $variable
	 * @param integer $dataType
	 * @param integer $length
	 * @param mixed $driverOptions
	 * @return boolean
	 */
	public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, int $length = null, $driverOptions = null): bool
	{
		$this->binds[$parameter] = &$variable;
		return true;
	}

	/**
	 * 把一个值绑定到一个参数
	 * @param mixed $parameter
	 * @param mixed $value
	 * @param integer $dataType
	 * @return boolean
	 */
	public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
	{
		$this->binds[$parameter] = $value;
		return true;
	}

	/**
	 * 关闭游标，使语句能再次被执行。
	 * @return boolean
	 */
	public function closeCursor(): bool
	{
		$this->cursor = -1;
		$this->data = null;
		return true;
	}

	/**
	 * 返回结果集中的列数
	 * @return int
	 */
	public function columnCount(): int
	{
		if($this->cursor >= 0)
		{
			return count(current($this->data));
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * 返回错误码
	 * @return mixed
	 */
	public function errorCode()
	{
		return $this->statement->errno;
	}
	
	/**
	 * 返回错误信息
	 * @return array
	 */
	public function errorInfo(): array
	{
		return [$this->statement->error];
	}

	/**
	 * 执行一条预处理语句
	 * @param array $inputParameters
	 * @return boolean
	 */
	public function execute(array $inputParameters = null): bool
	{
		if($this->cursor >= 0)
		{
			return false;
		}
		if(null === $inputParameters)
		{
			$inputParameters = $this->binds;
		}
		$result = $this->statement->execute($inputParameters);
		$this->binds = [];
		if(false === $result)
		{
			return false;
		}
		else
		{
			$this->data = $result;
			$this->cursor = 0;
			return true;
		}
	}

	/**
	 * 从结果集中获取下一行
	 * @param integer $fetchStyle
	 * @param integer $cursorOrientation
	 * @param integer $cursorOffset
	 * @return mixed
	 */
	public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
	{
		// 游标
		switch($cursorOrientation)
		{
			case \PDO::FETCH_ORI_NEXT:
				$row = $this->data[$this->cursor++] ?? false;
				break;
			case \PDO::FETCH_ORI_PRIOR:
				$row = $this->data[--$this->cursor] ?? false;
				break;
			case \PDO::FETCH_ORI_FIRST:
				$row = $this->data[$this->cursor = 0] ?? false;
				break;
			case \PDO::FETCH_ORI_LAST:
				$row = $this->data[$this->cursor = count($this->data) - 1] ?? false;
				break;
			case \PDO::FETCH_ORI_ABS:
				$row = $this->data[$this->cursor += $cursorOffset] ?? false;
				break;
			case \PDO::FETCH_ORI_REL:
				$row = $this->data[$this->cursor = $cursorOffset] ?? false;
				break;
			default:
				throw new \InvalidArgumentException('Statement fetch $cursorOrientation can not use ' . $cursorOrientation);
		}
		if(false === $row)
		{
			return false;
		}
		// 结果集
		return $this->fetchAllParser->getFetchParser()->parseRow($row, $fetchStyle, $this->columnBinds);
	}

	/**
	 * 返回一个包含结果集中所有行的数组
	 * @param integer $fetchStyle
	 * @param mixed $fetchArgument
	 * @return array
	 */
	public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = array()): array
	{
		return $this->fetchAllParser->parseAll($this->data, $fetchStyle);
	}

	/**
	 * 从结果集中的下一行返回单独的一列。
	 * @param integer $columnNumber
	 * @return mixed
	 */
	public function fetchColumn(int $columnNumber = 0)
	{
		return $this->fetch(\PDO::FETCH_NUM)[$columnNumber] ?? false;
	}
	
	/**
	 * 获取下一行并作为一个对象返回。
	 * @param string $class_name
	 * @param array $ctor_args
	 * @return mixed
	 */
	public function fetchObject(string $className = "stdClass", array $ctorArgs = null)
	{
		$object = new $className;
		foreach($this->fetch(\PDO::FETCH_ASSOC) as $name => $value)
		{
			$object->$name = $value;
		}
		return $object;
	}

	/**
	 * 检索一个语句属性
	 * @param mixed $attribute
	 * @return mixed
	 */
	public function getAttribute($attribute)
	{
		return null;
	}

	/**
	 * 设置属性
	 * @param mixed $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value): bool
	{
		return true;
	}

	/**
	 * 在一个多行集语句句柄中推进到下一个行集
	 * @return boolean
	 */
	public function nextRowset(): bool
	{
		return false;
	}

	/**
	 * 返回受上一个 SQL 语句影响的行数
	 * @return int
	 */
	public function rowCount(): int
	{
		return $this->statement->affected_rows;
	}

	/**
	 * 获取原对象实例
	 * @return object
	 */
	public function getInstance()
	{
		return $this->statement;
	}

	public function current()
	{
		return $this->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_ABS);
	}

	public function key()
	{
		return $this->cursor;;
	}

	public function next()
	{
		return $this->fetch(\PDO::FETCH_ASSOC);
	}

	public function rewind()
	{
		return $this->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_REL);
	}

	public function valid()
	{
		return false !== $this->current();
	}
}