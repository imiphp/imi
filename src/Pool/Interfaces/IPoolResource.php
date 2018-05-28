<?php
namespace Imi\Pool\Interfaces;

/**
 * 池子资源接口
 */
interface IPoolResource
{
	/**
	 * 打开
	 * @return boolean
	 */
	public function open();

	/**
	 * 关闭
	 * @return void
	 */
	public function close();

	/**
	 * 获取对象实例
	 * @return mixed
	 */
	public function getInstance();
}