<?php

declare(strict_types=1);

namespace Imi\Db\Drivers\PdoMysql;

use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Drivers\Base;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;

/**
 * PDO MySQL驱动.
 */
class Driver extends Base implements IDb
{
    /**
     * 连接对象
     *
     * @var \PDO
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
     * @var Statement
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
     * 'charset'    => '字符集',
     * 'options'    => [], // PDO连接选项
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
        if (!isset($option['options']))
        {
            $option['options'] = [];
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
     * 构建DNS字符串.
     *
     * @return string
     */
    protected function buildDSN()
    {
        $option = $this->option;
        if (isset($option['dsn']))
        {
            return $option['dsn'];
        }

        return 'mysql:'
                 . 'host=' . ($option['host'] ?? '127.0.0.1')
                 . ';port=' . ($option['port'] ?? '3306')
                 . ';dbname=' . ($option['database'] ?? '')
                 . ';unix_socket=' . ($option['unix_socket'] ?? '')
                 . ';charset=' . ($option['charset'] ?? 'utf8')
                 ;
    }

    /**
     * 是否已连接.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        try
        {
            return false !== $this->instance->getAttribute(\PDO::ATTR_SERVER_INFO);
        }
        catch (\Throwable $e)
        {
        }

        return false;
    }

    /**
     * 打开
     *
     * @return bool
     */
    public function open()
    {
        $option = $this->option;
        $this->instance = new \PDO($this->buildDSN(), $option['username'], $option['password'], $option['options']);

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
            $this->lastStmt = null;
        }
        $this->instance = null;
    }

    /**
     * 获取原对象实例.
     *
     * @return \PDO
     */
    public function getInstance(): \PDO
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
        if (!$this->inTransaction() && !$this->instance->beginTransaction())
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
        return $this->instance->inTransaction();
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
            return $this->lastStmt->errorCode();
        }
        else
        {
            return $this->instance->errorCode();
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
            return $this->lastStmt->errorInfo();
        }
        else
        {
            $errorInfo = $this->instance->errorInfo();
            if (null === $errorInfo[1] && null === $errorInfo[2])
            {
                return '';
            }

            return $errorInfo[1] . ':' . $errorInfo[2];
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

        $result = $this->instance->exec($sql);
        if (false === $result)
        {
            throw new DbException('SQL prepare error [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return $result;
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
        $queryResult = $this->query($sql);
        $result = [];
        do
        {
            try
            {
                $result[] = $queryResult->fetchAll();
            }
            catch (\PDOException $pe)
            {
                if ('SQLSTATE[HY000]: General error' === $pe->getMessage())
                {
                    $result[] = [];
                }
                else
                {
                    throw $pe;
                }
            }
        } while ($queryResult->nextRowset());

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
        return $this->instance->getAttribute($attribute);
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
        return $this->instance->setAttribute($attribute, $value);
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
        return $this->instance->lastInsertId($name);
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return null === $this->lastStmt ? 0 : $this->lastStmt->rowCount();
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
            $this->lastStmt = $lastStmt = $this->instance->prepare($sql, $driverOptions);
            if (false === $lastStmt)
            {
                throw new DbException('SQL prepare error [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
            }
            $stmt = BeanFactory::newInstance(Statement::class, $this, $lastStmt);
            if ($this->isCacheStatement && null === $stmtCache)
            {
                StatementManager::setNX($stmt, true);
            }
        }

        return $stmt;
    }

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
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

        return BeanFactory::newInstance(Statement::class, $this, $lastStmt);
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
