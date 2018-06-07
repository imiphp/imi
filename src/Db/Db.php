<?php
namespace Imi\Db;

use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\Interfaces\IQuery;

abstract class Db
{
	private static $defaultPoolName;

	/**
	 * 设置默认的数据库连接池名称
	 * @param string $poolName
	 * @return void
	 */
	public static function setDefaultPoolName($poolName)
	{
		static::$defaultPoolName = $poolName;
	}

	/**
	 * 获取默认的数据库连接池名称
	 * @return string
	 */
	public static function getDefaultPoolName()
	{
		return static::$defaultPoolName;
	}

	/**
	 * 获取数据库连接实例
	 * @param string $poolName 连接池名称
	 * @return \Imi\Db\Interfaces\IDb
	 */
	public static function getInstance($poolName = null): IDb
	{
		if(null === $poolName)
		{
			$poolName = static::$defaultPoolName;
		}
		
		return PoolManager::getResource($poolName)->getInstance();
	}

	/**
	 * 释放数据库连接实例
	 * @param \Imi\Db\Interfaces\IDb $db
	 * @return void
	 */
	public static function release($db)
	{
		$resource = RequestContext::get('poolResources.' . spl_object_hash($db));
		if(null !== $resource)
		{
			PoolManager::releaseResource($resource);
		}
	}

	/**
	 * 返回一个查询器
	 * @param string $poolName
	 * @return IQuery
	 */
	public static function query($poolName = null): IQuery
	{
		if(null === $poolName)
		{
			$poolName = static::$defaultPoolName;
		}

		return RequestContext::getBean('Query', static::getInstance($poolName));
	}
}