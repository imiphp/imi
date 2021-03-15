<?php

declare(strict_types=1);

namespace Imi\Db;

use Imi\App;
use Imi\Config;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Query;
use Imi\Db\Query\QueryType;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use Imi\RequestContext;

class Db
{
    private function __construct()
    {
    }

    /**
     * 获取新的数据库连接实例.
     *
     * @param string|null $poolName  连接池名称
     * @param int         $queryType 查询类型
     *
     * @return \Imi\Db\Interfaces\IDb
     */
    public static function getNewInstance(?string $poolName = null, int $queryType = QueryType::WRITE): IDb
    {
        $poolName = static::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getResource($poolName)->getInstance();
        }
        else
        {
            $config = Config::get('@app.db.connections.' . $poolName);
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }

            /** @var IDb $db */
            $db = App::getBean($config['dbClass'] ?? \Imi\Db\Drivers\PdoMysql\Driver::class, $config);
            $db->open();

            return $db;
        }
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个.
     *
     * @param string|null $poolName  连接池名称
     * @param int         $queryType 查询类型
     *
     * @return \Imi\Db\Interfaces\IDb
     */
    public static function getInstance(?string $poolName = null, int $queryType = QueryType::WRITE): IDb
    {
        $poolName = static::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            $requestContextKey = '__db.' . $poolName;
            $context = RequestContext::getContext();
            $db = $context[$requestContextKey] ?? null;
            if (null === $db)
            {
                $config = Config::get('@app.db.connections.' . $poolName);
                if (null === $config)
                {
                    throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
                }
                /** @var IDb $db */
                $db = $context[$requestContextKey] = App::getBean($config['dbClass'] ?? \Imi\Db\Drivers\PdoMysql\Driver::class, $config);
                $db->open();
            }

            return $db;
        }
    }

    /**
     * 释放数据库连接实例.
     *
     * @param \Imi\Db\Interfaces\IDb $db
     *
     * @return void
     */
    public static function release(IDb $db): void
    {
        $resource = RequestContext::get('poolResources')[spl_object_hash($db)] ?? null;
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 返回一个查询器.
     *
     * @param string|null $poolName
     * @param string|null $modelClass
     * @param int|null    $queryType
     *
     * @return IQuery
     */
    public static function query(?string $poolName = null, ?string $modelClass = null, ?int $queryType = null): IQuery
    {
        return App::getBean(Query::class, null, $modelClass, $poolName, $queryType);
    }

    /**
     * 处理连接池名称.
     *
     * @param string|null $poolName
     * @param int         $queryType
     *
     * @return string
     */
    private static function parsePoolName(?string $poolName = null, int $queryType = QueryType::WRITE): string
    {
        if (null === $poolName)
        {
            $poolName = static::getDefaultPoolName($queryType);
        }
        else
        {
            switch ($queryType)
            {
                case QueryType::READ:
                    $newPoolName = $poolName . '.slave';
                    if (PoolManager::exists($newPoolName))
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
     * 获取默认池子名称.
     *
     * @param int $queryType 查询类型
     *
     * @return string
     */
    public static function getDefaultPoolName(int $queryType = QueryType::WRITE): string
    {
        $poolName = Config::get('@currentServer.db.defaultPool');
        if (null !== $poolName)
        {
            $poolName = static::parsePoolName($poolName, $queryType);
        }

        return $poolName;
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable    $callable
     * @param string|null $poolName
     * @param int         $queryType
     *
     * @return mixed
     */
    public static function use(callable $callable, ?string $poolName = null, int $queryType = QueryType::WRITE)
    {
        $poolName = static::parsePoolName($poolName, $queryType);

        if (PoolManager::exists($poolName))
        {
            return PoolManager::use($poolName, function (IPoolResource $resource, IDb $db) use ($callable) {
                return $callable($db);
            });
        }
        else
        {
            return $callable(static::getInstance($poolName));
        }
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放，自动开启/提交/回滚事务
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable    $callable
     * @param string|null $poolName
     * @param int         $queryType
     *
     * @return mixed
     */
    public static function transUse(callable $callable, ?string $poolName = null, int $queryType = QueryType::WRITE)
    {
        $poolName = static::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::use($poolName, function (IPoolResource $resource, IDb $db) use ($callable) {
                return static::trans($db, $callable);
            });
        }
        else
        {
            return static::trans(static::getInstance($poolName), $callable);
        }
    }

    /**
     * 使用回调来使用当前上下文中的资源，无需手动释放，自动开启/提交/回滚事务
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @param callable    $callable
     * @param string|null $poolName
     * @param int         $queryType
     *
     * @return mixed
     */
    public static function transContext(callable $callable, ?string $poolName = null, int $queryType = QueryType::WRITE)
    {
        $db = static::getInstance($poolName, $queryType);

        return static::trans($db, $callable);
    }

    /**
     * 事务处理，自动开启/提交/回滚事务
     *
     * @param IDb      $db
     * @param callable $callable
     *
     * @return mixed
     */
    public static function trans(IDb $db, callable $callable)
    {
        try
        {
            $db->beginTransaction();
            $result = $callable($db); // 调用回调
            $db->commit();

            return $result;
        }
        catch (\Throwable $th)
        {
            // 回滚事务
            if ($db->inTransaction())
            {
                $db->rollBack();
            }
            throw $th;
        }
    }
}
