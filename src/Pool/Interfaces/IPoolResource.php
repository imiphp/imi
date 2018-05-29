<?php
namespace Imi\Pool\Interfaces;

/**
 * 池子资源接口
 */
interface IPoolResource
{
	/**
	 * 打开
	 * @param callable $callback
	 * @return boolean
	 */
	public function open($callback = null);

	/**
	 * 关闭
	 * @param callable $callback
	 * @return void
	 */
	public function close();

	/**
	 * 获取对象实例
	 * @return mixed
	 */
	public function getInstance();
}