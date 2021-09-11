<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\Swoole;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Config;
use Imi\Db\Exception\DbException;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBase;
use Imi\Pgsql\Db\Util\SqlUtil;
use Swoole\Coroutine\PostgreSQL;

/**
 * Swoole Coroutine PostgreSQL 驱动.
 *
 * @Bean("SwoolePgsqlDriver")
 */
class Driver extends PgsqlBase implements IPgsqlDb
{
    /**
     * 连接对象
     */
    protected ?PostgreSQL $instance = null;

    /**
     * 最后执行过的SQL语句.
     */
    protected string $lastSql = '';

    /**
     * 最后查询结果.
     *
     * @var resource|null
     */
    protected $lastQueryResult = null;

    /**
     * 是否缓存 Statement.
     */
    protected bool $isCacheStatement = false;

    /**
     * 事务管理.
     */
    protected Transaction $transaction;

    /**
     * 自增.
     */
    protected int $statementIncr = 0;

    /**
     * 参数格式：
     * [
     * 'host'       => 'PostgreSQL IP地址',
     * 'username'   => '数据用户',
     * 'password'   => '数据库密码',
     * 'database'   => '数据库名',
     * 'port'       => 'PostgreSQL端口 默认5432 可选参数',
     * 'options'    => [], // 其它连接选项
     * ].
     */
    public function __construct(array $option = [])
    {
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

        return $instance && $instance->query('select 1');
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
        $otherOptionsContent = '';
        foreach ($option['options'] ?? [] as $k => $v)
        {
            $otherOptionsContent .= ' ' . $k . '=' . $v;
        }

        return 'host=' . ($option['host'] ?? '127.0.0.1')
                 . ' port=' . ($option['port'] ?? '5432')
                 . ' dbname=' . ($option['database'] ?? '')
                 . ' user=' . ($option['username'] ?? '')
                 . ' password=' . ($option['password'] ?? '')
                 . $otherOptionsContent
                 ;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $this->statementIncr = 0;
        $this->instance = $instance = new PostgreSQL();

        $result = $instance->connect($this->buildDSN());
        if ($result)
        {
            $this->execInitSqls();
        }

        return $result;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        StatementManager::clear($this);
        if (null !== $this->lastQueryResult)
        {
            $this->lastQueryResult = null;
        }
        if (null !== $this->instance)
        {
            $this->instance = null;
        }
    }

    /**
     * 获取原对象实例.
     */
    public function getInstance(): PostgreSQL
    {
        return $this->instance;
    }

    /**
     * 启动一个事务
     */
    public function beginTransaction(): bool
    {
        if (!$this->inTransaction() && !$this->instance->query('begin'))
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
        return $this->instance->query('commit') && $this->transaction->commit();
    }

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     */
    public function rollBack(?int $levels = null): bool
    {
        if (null === $levels)
        {
            $result = $this->instance->query('rollback');
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

        return (bool) $result;
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
        return $this->instance->errCode ?? 0;
    }

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string
    {
        return $this->instance->error ?? '';
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
        $this->lastQueryResult = $instance->query($sql);

        return $instance->affectedRows($this->lastQueryResult);
    }

    /**
     * 批量执行 SQL，返回查询结果.
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
        if (null === $name)
        {
            return (string) $this->query('select LASTVAL()')->fetchColumn();
        }
        else
        {
            $stmt = $this->prepare('SELECT CURRVAL($1)');
            $stmt->execute([$name]);

            return (string) $stmt->fetchColumn();
        }
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return $this->instance->affectedRows($this->lastQueryResult);
    }

    /**
     * 准备执行语句并返回一个语句对象
     */
    public function prepare(string $sql, array $driverOptions = []): IPgsqlStatement
    {
        if ($this->isCacheStatement && $stmtCache = StatementManager::get($this, $sql))
        {
            $stmt = $stmtCache['statement'];
        }
        else
        {
            $this->lastSql = $sql;
            $parsedSql = SqlUtil::parseSqlWithParams($sql, $sqlParamsMap);
            $statementName = (string) (++$this->statementIncr);
            $this->lastQueryResult = $queryResult = $this->instance->prepare($statementName, $parsedSql);
            if (false === $queryResult)
            {
                throw new DbException('SQL prepare error: ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
            }
            $stmt = App::getBean(Statement::class, $this, null, $sql, $statementName, $sqlParamsMap);
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
    public function query(string $sql): IPgsqlStatement
    {
        $this->lastSql = $sql;
        $this->lastQueryResult = $queryResult = $this->instance->query($sql);
        if (false === $queryResult)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return App::getBean(Statement::class, $this, $queryResult, $sql);
    }

    /**
     * Get 事务管理.
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
