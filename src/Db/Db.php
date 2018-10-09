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
use Imi\Db\Query\QueryType;

abstract class Db
{
    /**
     * 获取新的数据库连接实例
     * @param string $poolName 连接池名称
     * @param int $queryType 查询类型
     * @return \Imi\Db\Interfaces\IDb
     */
    public static function getNewInstance($poolName = null, $queryType = QueryType::WRITE): IDb
    {
        return PoolManager::getResource(static::parsePoolName($poolName, $queryType))->getInstance();
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个
     * @param string $poolName 连接池名称
     * @param int $queryType 查询类型
     * @return \Imi\Db\Interfaces\IDb
     */
    public static function getInstance($poolName = null, $queryType = QueryType::WRITE): IDb
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName, $queryType))->getInstance();
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
     * @param int $queryType
     * @return IQuery
     */
    public static function query($poolName = null, $modelClass = null, $queryType = null): IQuery
    {
        return BeanFactory::newInstance(Query::class, $poolName, $modelClass, $queryType);
    }

    /**
     * 处理连接池 名称
     *
     * @param string $poolName
     * @param int $queryType
     * @return string
     */
    private static function parsePoolName($poolName = null, $queryType = QueryType::WRITE)
    {
        if(null === $poolName)
        {
            $poolName = static::getDefaultPoolName($queryType);
        }
        else
        {
            switch($queryType)
            {
                case QueryType::READ:
                    $newPoolName = $poolName . '.slave';
                    if(PoolManager::exists($newPoolName))
                    {
                        $poolName = $newPoolName;
                    }
                    break;
                case QueryType::WRITE:
                default:
                    // 保持原样不做任何处理
            }
        }
        return $poolName;
    }

    /**
     * 获取默认池子名称
     * @param int $queryType 查询类型
     * @return string
     */
    public static function getDefaultPoolName($queryType = QueryType::WRITE)
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
        $poolName = Helper::getMain($namespace)->getConfig()['db']['defaultPool'] ?? null;
        if(null !== $poolName)
        {
            $poolName = static::parsePoolName($poolName, $queryType);
        }
        return $poolName;
    }
}