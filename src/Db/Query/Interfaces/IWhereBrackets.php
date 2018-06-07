<?php
namespace Imi\Db\Query\Interfaces;

interface IWhereBrackets extends IBaseWhere
{
	/**
	 * 回调
	 * @return callable
	 */
	public function getCallback(): callable;

	/**
	 * 逻辑运算符
	 * @return string
	 */
	public function getLogicalOperator(): string;

	/**
	 * 回调
	 * @param callable $callback
	 * @return void
	 */
	public function setCallback(callable $callback);

	/**
	 * 逻辑运算符
	 * @param string $logicalOperator
	 * @return void
	 */
	public function setLogicalOperator(string $logicalOperator);
}