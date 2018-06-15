<?php
namespace Imi\Db;

use Imi\RequestContext;
use Imi\Bean\BeanFactory;
use Imi\Pool\PoolManager;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Query;
use Imi\Main\Helper;
use Imi\App;

abstract class Db
{
	/**
	 * 获取新的数据库连接实例
	 * @param string $poolName 连接池名称
	 * @return \Imi\Db\Interfaces\IDb
	 */
	public static function getNewInstance($poolName = null): IDb
	{
		if(null === $poolName)
		{
			$poolName = static::getDefaultPoolName();
		}
		
		return PoolManager::getResource($poolName)->getInstance();
	}

	/**
	 * 获取数据库连接实例，每个RequestContext中共用一个
	 * @param string $poolName 连接池名称
	 * @return \Imi\Db\Interfaces\IDb
	 */
	public static function getInstance($poolName = null): IDb
	{
		if(null === $poolName)
		{
			$poolName = static::getDefaultPoolName();
		}
		
		return PoolManager::getRequestContextResource($poolName)->getInstance();
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
	 * @param string $modelClass
	 * @return IQuery
	 */
	public static function query($poolName = null, $modelClass = null): IQuery
	{
		if(null === $poolName)
		{
			$poolName = static::getDefaultPoolName();
		}

		return BeanFactory::newInstance(Query::class, static::getInstance($poolName), $modelClass);
	}

	/**
	 * 获取默认池子名称
	 * @return string
	 */
	public static function getDefaultPoolName()
	{
		$namespace = null;
		if(RequestContext::exsits())
		{
			try{
				$namespace = RequestContext::getServer()->getConfig()['namespace'];
				$defaultPool = Helper::getMain($namespace)->getConfig()['db']['defaultPool'] ?? null;
				if(null === $defaultPool)
				{
					$namespace = null;
				}
			}
			catch(\Throwable $ex)
			{
				$namespace = null;
			}
		}
		if(null === $namespace)
		{
			$namespace = App::getNamespace();
		}
		return Helper::getMain($namespace)->getConfig()['db']['defaultPool'] ?? null;
	}
}