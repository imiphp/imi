<?php

declare(strict_types=1);

namespace Imi\Db\Drivers\Swoole;

use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Drivers\Base;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use Imi\Db\Util\SqlUtil;
use Swoole\Coroutine\MySQL;

/**
 * Swoole Coroutine MySQL 驱动.
 */
class Driver extends Base implements IDb
{
    /**
     * 连接对象
     *
     * @var \Swoole\Coroutine\MySQL
     */
    protected $instance;

    /**
     * 连接配置.
     *
     * @var array
     */
    protected $option;

    /**
     * 最后执行过的SQL语句.
     *
     * @var string
     */
    protected $lastSql = '';

    /**
     * Statement.
     *
     * @var \Swoole\Coroutine\MySQL\Statement
     */
    protected $lastStmt;

    /**
     * 是否缓存 Statement.
     *
     * @var bool
     */
    protected $isCacheStatement;

    /**
     * 事务管理.
     *
     * @var \Imi\Db\Transaction\Transaction
     */
    protected $transaction;

    /**
     * 参数格式：
     * [
     * 'host'       => 'MySQL IP地址',
     * 'username'   => '数据用户',
     * 'password'   => '数据库密码',
     * 'database'   => '数据库名',
     * 'port'       => 'MySQL端口 默认3306 可选参数',
     * 'timeout'    => '建立连接超时时间',
     * 'charset'    => '字符集',
     * 'strict_type'=> true, // 开启严格模式，query方法返回的数据也将转为强类型
     * 'fetch_mode' => false, // 开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
     * 'options'    => [], // 其它选项
     * ].
     *
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->option = $option;
        $this->isCacheStatement = Config::get('@app.db.statement.cache', true);
        $this->transaction = new Transaction();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * 是否已连接.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        $instance = $this->instance;

        return $instance && $instance->query('select 1');
    }

    /**
     * 打开
     *
     * @return bool
     */
    public function open()
    {
        $this->instance = $instance = new MySQL();
        $option = $this->option;
        $serverConfig = [
            'host'          => $option['host'] ?? '127.0.0.1',
            'port'          => $option['port'] ?? 3306,
            'user'          => $option['username'] ?? 'root',
            'password'      => $option['password'] ?? '',
            'database'      => $option['database'] ?? '',
            'timeout'       => $option['timeout'] ?? null,
            'charset'       => $option['charset'] ?? 'utf8',
            'strict_type'   => $option['strict_type'] ?? true,
            'fetch_mode'    => $option['fetch_mode'] ?? false,
        ];
        if (isset($option['options']))
        {
            $serverConfig = array_merge($serverConfig, $option['options']);
        }

        return $instance->connect($serverConfig);
    }

    /**
     * 关闭.
     *
     * @return void
     */
    public function close()
    {
        StatementManager::clear($this);
        if (null !== $this->lastStmt)
        {
            $this->lastStmt = null;
        }
        if (null !== $this->instance)
        {
            $this->instance->close();
            $this->instance = null;
        }
    }

    /**
     * 获取原对象实例.
     *
     * @return \Swoole\Coroutine\MySQL
     */
    public function getInstance(): \Swoole\Coroutine\MySQL
    {
        return $this->instance;
    }

    /**
     * 启动一个事务
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        if (!$this->inTransaction() && !$this->instance->begin())
        {
            return false;
        }
        $this->exec('SAVEPOINT P' . $this->getTransactionLevels());
        $this->transaction->beginTransaction();

        return true;
    }

    /**
     * 提交一个事务
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->instance->commit() && $this->transaction->commit();
    }

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     *
     * @param int $levels
     *
     * @return bool
     */
    public function rollBack($levels = null): bool
    {
        if (null === $levels)
        {
            $result = $this->instance->rollback();
        }
        else
        {
            $this->exec('ROLLBACK TO P' . ($this->getTransactionLevels()));
            $result = true;
        }
        if ($result)
        {
            $this->transaction->rollBack($levels);
        }

        return $result;
    }

    /**
     * 获取事务层数.
     *
     * @return int
     */
    public function getTransactionLevels(): int
    {
        return $this->transaction->getTransactionLevels();
    }

    /**
     * 检查是否在一个事务内.
     *
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->transaction->getTransactionLevels() > 0;
    }

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode()
    {
        if ($this->lastStmt)
        {
            return $this->lastStmt->errno;
        }
        else
        {
            return $this->instance->errno;
        }
    }

    /**
     * 返回错误信息.
     *
     * @return array
     */
    public function errorInfo(): string
    {
        if ($this->lastStmt)
        {
            return $this->lastStmt->error;
        }
        else
        {
            return $this->instance->error;
        }
    }

    /**
     * 获取最后一条执行的SQL语句.
     *
     * @return string
     */
    public function lastSql()
    {
        return $this->lastSql;
    }

    /**
     * 执行一条 SQL 语句，并返回受影响的行数.
     *
     * @param string $sql
     *
     * @return int
     */
    public function exec(string $sql): int
    {
        $this->lastStmt = null;
        $this->lastSql = $sql;
        $instance = $this->instance;
        $instance->query($sql);

        return $instance->affected_rows;
    }

    /**
     * 批量执行 SQL，返回查询结果.
     *
     * @param string $sql
     *
     * @return array
     */
    public function batchExec(string $sql): array
    {
        $result = [];
        foreach (SqlUtil::parseMultiSql($sql) as $itemSql)
        {
            $queryResult = $this->query($itemSql);
            $result[] = $queryResult->fetchAll();
        }

        return $result;
    }

    /**
     * 取回一个数据库连接的属性.
     *
     * @param mixed $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /**
     * 设置属性.
     *
     * @param mixed $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        return true;
    }

    /**
     * 返回最后插入行的ID或序列值
     *
     * @param string $name
     *
     * @return string
     */
    public function lastInsertId(string $name = null)
    {
        return $this->instance->insert_id;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return null === $this->lastStmt ? $this->instance->affected_rows : $this->lastStmt->affected_rows;
    }

    /**
     * 准备执行语句并返回一个语句对象
     *
     * @param string $sql
     * @param array  $driverOptions
     *
     * @return IStatement
     *
     * @throws DbException
     */
    public function prepare(string $sql, array $driverOptions = [])
    {
        if ($this->isCacheStatement && $stmtCache = StatementManager::get($this, $sql))
        {
            $stmt = $stmtCache['statement'];
        }
        else
        {
            $this->lastSql = $sql;
            $parsedSql = SqlUtil::parseSqlWithColonParams($sql, $sqlParamsMap);
            $this->lastStmt = $lastStmt = $this->instance->prepare($parsedSql);
            if (false === $lastStmt)
            {
                throw new DbException('SQL prepare error [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
            }
            $stmt = BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql, $sqlParamsMap);
            if ($this->isCacheStatement && null === $stmtCache)
            {
                StatementManager::setNX($stmt, true);
            }
        }

        return $stmt;
    }

    /**
     * 执行一条SQL语句，返回一个结果集作为Statement对象
     *
     * @param string $sql
     *
     * @return IStatement
     *
     * @throws DbException
     */
    public function query(string $sql)
    {
        $this->lastSql = $sql;
        $this->lastStmt = $lastStmt = $this->instance->query($sql);
        if (false === $lastStmt)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql);
    }

    /**
     * Get 事务管理.
     *
     * @return \Imi\Db\Transaction\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
