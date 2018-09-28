<?php
namespace Imi\Pool\Interfaces;

use Imi\Resource\IResource;

/**
 * 池子资源接口
 */
interface IPoolResource extends IResource
{
    /**
	 * 获取对象实例
	 * @return mixed
	 */
	public function getInstance();

	/**
	 * 获取池子实例
	 * @return IPool
	 */
	public function getPool(): IPool;


	/**
	 * 检查资源是否可用
	 * @return bool
	 */
	public function checkState(): bool;
}