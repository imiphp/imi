<?php

namespace Imi\Db\Drivers\Mysqli;

use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Drivers\Base;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use Imi\Db\Util\SqlUtil;

/**
 * mysqli MySQL驱动.
 */
class Driver extends Base implements IDb
{
    /**
     * 连接对象
     *
     * @var \mysqli
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
     * @var \mysqli_stmt
     */
    protected $lastStmt;

    /**
     * result.
     *
     * @var \mysqli_result
     */
    protected $lastResult;

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
     * 'charset'    => '字符集',
     * ].
     *
     * @param array $option
     */
    public function __construct($option = [])
    {
        if (!isset($option['username']))
        {
            $option['username'] = 'root';
        }
        if (!isset($option['password']))
        {
            $option['password'] = '';
        }
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
        return $this->instance->ping();
    }

    /**
     * 打开
     *
     * @return bool
     */
    public function open()
    {
        $option = $this->option;
        $this->instance = new \mysqli($option['host'] ?? '127.0.0.1', $option['username'], $option['password'], $option['database'], $option['port'] ?? 3306);

        return true;
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
            $this->lastStmt->close();
            $this->lastStmt = null;
        }
        if (null !== $this->lastResult)
        {
            $this->lastResult->close();
            $this->lastResult = null;
        }
        $this->instance = null;
    }

    /**
     * 获取原对象实例.
     *
     * @return \mysqli
     */
    public function getInstance(): \mysqli
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
        if (!$this->inTransaction() && !$this->instance->begin_transaction())
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
        $this->lastSql = $sql;
        $instance = $this->instance;

        return $instance->query($sql) ? $instance->affected_rows : 0;
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
        $this->lastSql = $sql;
        $instance = $this->instance;
        $this->lastResult = $lastResult = $instance->multi_query($sql);
        if (false === $lastResult)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }
        $results = [];
        do
        {
            $result = $instance->store_result();
            if ($result)
            {
                $results[] = $result->fetch_all(\MYSQLI_ASSOC);
                $result->close();
            }
            else
            {
                $results[] = [];
            }
        } while ($instance->next_result());

        return $results;
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
        return null === $this->lastStmt ? 0 : $this->lastStmt->affected_rows;
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
            $stmt = BeanFactory::newInstance(Statement::class, $this, $lastStmt, null, $sql, $sqlParamsMap);
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
        $this->lastResult = $lastResult = $this->instance->query($sql);
        if (false === $lastResult)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return BeanFactory::newInstance(Statement::class, $this, null, $lastResult, $sql);
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
