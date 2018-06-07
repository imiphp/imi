<?php
namespace Imi\Db\Query\Interfaces;

interface IWhere extends IBaseWhere
{
	/**
	 * 字段名
	 * @return string
	 */
	public function getFieldName(): string;

	/**
	 * 比较符
	 * @return string
	 */
	public function getOperation(): string;

	/**
	 * 值
	 * @return mixed
	 */
	public function getValue();

	/**
	 * 逻辑运算符
	 * @return string
	 */
	public function getLogicalOperator(): string;

	/**
	 * 字段名
	 * @param string $fieldName
	 * @return void
	 */
	public function setFieldName(string $fieldName);

	/**
	 * 比较符
	 * @param string $operation
	 * @return void
	 */
	public function setOperation(string $operation);

	/**
	 * 值
	 * @param mixed $value
	 * @return void
	 */
	public function setValue($value);

	/**
	 * 逻辑运算符
	 * @param string $logicalOperator
	 * @return void
	 */
	public function setLogicalOperator(string $logicalOperator);
}