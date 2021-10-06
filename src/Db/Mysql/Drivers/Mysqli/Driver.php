<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBase;
use Imi\Db\Mysql\Util\SqlUtil;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use mysqli;

/**
 * mysqli MySQL驱动.
 *
 * @Bean("MysqliDriver")
 */
class Driver extends MysqlBase implements IMysqlDb
{
    /**
     * 连接对象
     */
    protected ?mysqli $instance = null;

    /**
     * 最后执行过的SQL语句.
     */
    protected string $lastSql = '';

    /**
     * Statement.
     *
     * @var \mysqli_stmt|false|null
     */
    protected $lastStmt = null;

    /**
     * result.
     *
     * @var \mysqli_result|bool|null
     */
    protected $lastResult = null;

    /**
     * 是否缓存 Statement.
     */
    protected bool $isCacheStatement = false;

    /**
     * 事务管理.
     */
    protected Transaction $transaction;

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
     */
    public function __construct(array $option = [])
    {
        $option['username'] ??= 'root';
        $option['password'] ??= '';
        parent::__construct($option);
        $this->isCacheStatement = Config::get('@app.db.statement.cache', true);
        $this->transaction = new Transaction();
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        return (bool) $this->instance;
    }

    /**
     * ping 检查是否已连接.
     */
    public function ping(): bool
    {
        $instance = $this->instance;

        return $instance && $instance->ping();
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $option = $this->option;
        $this->instance = $instance = new \mysqli($option['host'] ?? '127.0.0.1', $option['username'], $option['password'], $option['database'], $option['port'] ?? 3306);
        $instance->set_charset($option['charset'] ?? 'utf8');
        $this->execInitSqls();

        return true;
    }

    /**
     * 关闭.
     */
    public function close(): void
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
     */
    public function getInstance(): mysqli
    {
        return $this->instance;
    }

    /**
     * 启动一个事务
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
     */
    public function commit(): bool
    {
        return $this->instance->commit() && $this->transaction->commit();
    }

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     */
    public function rollBack(?int $levels = null): bool
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
     */
    public function getTransactionLevels(): int
    {
        return $this->transaction->getTransactionLevels();
    }

    /**
     * 检查是否在一个事务内.
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
     */
    public function lastSql(): string
    {
        return $this->lastSql;
    }

    /**
     * 执行一条 SQL 语句，并返回受影响的行数.
     */
    public function exec(string $sql): int
    {
        $this->lastSql = $sql;
        $instance = $this->instance;

        return $instance->query($sql) ? $instance->affected_rows : 0;
    }

    /**
     * 批量执行 SQL，返回查询结果.
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
        while (true)
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
            if ($instance->more_results())
            {
                $instance->next_result();
            }
            else
            {
                break;
            }
        }

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
     */
    public function setAttribute($attribute, $value): bool
    {
        return true;
    }

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) $this->instance->insert_id;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return null === $this->lastStmt ? 0 : $this->lastStmt->affected_rows;
    }

    /**
     * 准备执行语句并返回一个语句对象
     */
    public function prepare(string $sql, array $driverOptions = []): IMysqlStatement
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
            $stmt = App::getBean(Statement::class, $this, $lastStmt, null, $sql, $sqlParamsMap);
            if ($this->isCacheStatement && !isset($stmtCache))
            {
                StatementManager::setNX($stmt, true);
            }
        }

        return $stmt;
    }

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     */
    public function query(string $sql): IMysqlStatement
    {
        $this->lastSql = $sql;
        $this->lastResult = $lastResult = $this->instance->query($sql);
        if (false === $lastResult)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return App::getBean(Statement::class, $this, null, $lastResult, $sql);
    }

    /**
     * Get 事务管理.
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
