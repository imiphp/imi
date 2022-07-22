<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Exception\DbException;
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
class Driver extends MysqlBase
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
    protected ?Transaction $transaction = null;

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
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(): bool
    {
        return (bool) $this->instance;
    }

    /**
     * {@inheritDoc}
     */
    public function ping(): bool
    {
        $instance = $this->instance;
        if (!$instance)
        {
            return false;
        }
        if ($instance->query('select 1'))
        {
            return true;
        }
        if ($this->checkCodeIsOffline($instance->errno))
        {
            $this->close();
        }

        return false;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
        if ($this->transaction)
        {
            $this->transaction->init();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): ?mysqli
    {
        return $this->instance;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): bool
    {
        if (!$this->inTransaction() && !$this->instance->begin_transaction())
        {
            if ($this->checkCodeIsOffline($this->instance->errno))
            {
                $this->close();
            }

            return false;
        }
        $this->exec('SAVEPOINT P' . $this->getTransactionLevels());
        $this->getTransaction()->beginTransaction();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): bool
    {
        if (!$this->instance->commit())
        {
            if ($this->checkCodeIsOffline($this->instance->errno))
            {
                $this->close();
            }

            return false;
        }

        return $this->getTransaction()->commit();
    }

    /**
     * {@inheritDoc}
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
            $this->getTransaction()->rollBack($levels);
        }
        elseif ($this->checkCodeIsOffline($this->instance->errno))
        {
            $this->close();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionLevels(): int
    {
        return $this->getTransaction()->getTransactionLevels();
    }

    /**
     * {@inheritDoc}
     */
    public function inTransaction(): bool
    {
        return $this->getTransaction()->getTransactionLevels() > 0;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function lastSql(): string
    {
        return $this->lastSql;
    }

    /**
     * {@inheritDoc}
     */
    public function exec(string $sql): int
    {
        $this->lastSql = $sql;
        $instance = $this->instance;
        $this->lastStmt = null;
        if (false === $instance->query($sql) && $this->checkCodeIsOffline($this->instance->errno))
        {
            $this->close();

            return 0;
        }

        return $instance->affected_rows;
    }

    /**
     * {@inheritDoc}
     */
    public function batchExec(string $sql): array
    {
        $this->lastSql = $sql;
        $instance = $this->instance;
        $this->lastResult = $lastResult = $instance->multi_query($sql);
        if (false === $lastResult)
        {
            $errorCode = $this->errorCode();
            $errorInfo = $this->errorInfo();
            if ($this->checkCodeIsOffline($errorCode))
            {
                $this->close();
            }
            throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
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
     * {@inheritDoc}
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attribute, $value): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) $this->instance->insert_id;
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return null === $this->lastStmt ? 0 : $this->lastStmt->affected_rows;
    }

    /**
     * {@inheritDoc}
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
                $errorCode = $this->errorCode();
                $errorInfo = $this->errorInfo();
                if ($this->checkCodeIsOffline($errorCode))
                {
                    $this->close();
                }
                throw new DbException('SQL prepare error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
            }
            $stmt = BeanFactory::newInstance(Statement::class, $this, $lastStmt, null, $sql, $sqlParamsMap);
            if ($this->isCacheStatement && !isset($stmtCache))
            {
                StatementManager::setNX($stmt, true);
            }
        }

        return $stmt;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql): IMysqlStatement
    {
        $this->lastSql = $sql;
        $this->lastStmt = null;
        $this->lastResult = $lastResult = $this->instance->query($sql);
        if (false === $lastResult)
        {
            $errorCode = $this->errorCode();
            $errorInfo = $this->errorInfo();
            if ($this->checkCodeIsOffline($errorCode))
            {
                $this->close();
            }
            throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return BeanFactory::newInstance(Statement::class, $this, null, $lastResult, $sql);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction ??= new Transaction();
    }
}
