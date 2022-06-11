<?php

declare(strict_types=1);

namespace Imi\Db;

use Imi\App;
use Imi\Config;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\QueryType;
use Imi\Db\Query\Result;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use Imi\Timer\Timer;

class Db
{
    /**
     * 连接配置.
     */
    private static ?array $connections = null;

    private function __construct()
    {
    }

    /**
     * 获取新的数据库连接实例.
     *
     * @param string|null $poolName  连接池名称
     * @param int         $queryType 查询类型
     */
    public static function getNewInstance(?string $poolName = null, int $queryType = QueryType::WRITE): IDb
    {
        $poolName = self::parsePoolName($poolName, $queryType);
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
            $db = App::getBean($config['dbClass'] ?? 'PdoMysqlDriver', $config);
            $db->open();

            return $db;
        }
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个.
     *
     * @param string|null $poolName  连接池名称
     * @param int         $queryType 查询类型
     */
    public static function getInstance(?string $poolName = null, int $queryType = QueryType::WRITE): IDb
    {
        $poolName = self::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            if (null === self::$connections)
            {
                self::$connections = Config::get('@app.db.connections');
            }
            $config = self::$connections[$poolName] ?? null;
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }
            $requestContextKey = '__db.' . $poolName;
            $requestContext = RequestContext::getContext();
            if (isset($requestContext[$requestContextKey]))
            {
                $db = $requestContext[$requestContextKey];
            }
            else
            {
                /** @var IDb|null $db */
                $db = App::get($requestContextKey);
            }
            if (null === $db || !$db->isConnected())
            {
                /** @var IDb $db */
                $db = App::getBean($config['dbClass'] ?? 'PdoMysqlDriver', $config);
                if (!$db->open())
                {
                    throw new DbException('Db connect error: [' . $db->errorCode() . '] ' . $db->errorInfo());
                }
                App::set($requestContextKey, $db);
                if (($heartbeatInterval = $config['heartbeatInterval'] ?? 0) > 0)
                {
                    Timer::tick((int) ($heartbeatInterval * 1000), function () use ($requestContextKey) {
                        /** @var IDb|null $db */
                        $db = App::get($requestContextKey);
                        if (!$db)
                        {
                            return;
                        }
                        self::heartbeat($db);
                    });
                }
            }
            elseif ($config['checkStateWhenGetResource'] ?? true)
            {
                self::heartbeat($db);
            }

            return $requestContext[$requestContextKey] = $db;
        }
    }

    /**
     * 获取数据库连接实例配置.
     */
    public static function getInstanceConfig(?string $poolName = null, int $queryType = QueryType::WRITE): array
    {
        $poolName = self::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getInstance($poolName)->getResourceConfig()[0] ?? [];
        }
        else
        {
            if (null === self::$connections)
            {
                self::$connections = Config::get('@app.db.connections');
            }
            $config = self::$connections[$poolName] ?? null;
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }

            return $config;
        }
    }

    /**
     * 心跳.
     */
    public static function heartbeat(IDb $db): void
    {
        if (!$db->ping())
        {
            $db->close();
            $db->open();
        }
    }

    /**
     * 释放数据库连接实例.
     */
    public static function release(IDb $db): void
    {
        $resource = RequestContext::get('poolResources')[spl_object_id($db)] ?? null;
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 返回一个查询器.
     */
    public static function query(?string $poolName = null, ?string $modelClass = null, ?int $queryType = null): IQuery
    {
        return self::getInstance($poolName, $queryType ?? QueryType::WRITE)->createQuery($modelClass);
    }

    /**
     * 处理连接池名称.
     */
    private static function parsePoolName(?string $poolName = null, int $queryType = QueryType::WRITE): string
    {
        if (null === $poolName || '' === $poolName)
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
     */
    public static function getDefaultPoolName(int $queryType = QueryType::WRITE): string
    {
        $poolName = Config::get('@currentServer.db.defaultPool');
        if (null !== $poolName)
        {
            $poolName = self::parsePoolName($poolName, $queryType);
        }

        return $poolName;
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @return mixed
     */
    public static function use(callable $callable, ?string $poolName = null, int $queryType = QueryType::WRITE)
    {
        $poolName = self::parsePoolName($poolName, $queryType);

        if (PoolManager::exists($poolName))
        {
            return PoolManager::use($poolName, static fn (IPoolResource $resource, IDb $db) => $callable($db));
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
     * @return mixed
     */
    public static function transUse(callable $callable, ?string $poolName = null, int $queryType = QueryType::WRITE)
    {
        $poolName = self::parsePoolName($poolName, $queryType);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::use($poolName, fn (IPoolResource $resource, IDb $db) => static::trans($db, $callable));
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
            if ($db->inTransaction() && $db->isConnected())
            {
                $db->rollBack();
            }
            throw $th;
        }
    }

    /**
     * 执行 SQL 并返回受影响的行数.
     */
    public static function exec(string $sql, array $bindValues = [], ?string $poolName = null, int $queryType = QueryType::WRITE): int
    {
        if ($bindValues)
        {
            $stmt = self::getInstance($poolName, $queryType)->prepare($sql);
            if ($stmt->execute($bindValues))
            {
                return $stmt->rowCount();
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return self::getInstance($poolName, $queryType)->exec($sql);
        }
    }

    /**
     * 执行 SQL 返回结果.
     */
    public static function select(string $sql, array $bindValues = [], ?string $poolName = null, int $queryType = QueryType::WRITE): ?IResult
    {
        $db = self::getInstance($poolName, $queryType);
        if ($bindValues)
        {
            $stmt = $db->prepare($sql);
            if (!$stmt->execute($bindValues))
            {
                return new Result(false);
            }
        }
        else
        {
            $stmt = $db->query($sql);
        }

        return new Result($stmt, null, true);
    }

    /**
     * 准备执行语句并返回一个语句对象
     */
    public static function prepare(string $sql, ?string $poolName = null, int $queryType = QueryType::WRITE): IStatement
    {
        return self::getInstance($poolName, $queryType)->prepare($sql);
    }
}
