<?php

declare(strict_types=1);

namespace Imi\Swoole\Db\Driver\Swoole;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBase;
use Imi\Db\Mysql\Util\SqlUtil;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use Swoole\Coroutine\MySQL;

/**
 * Swoole Coroutine MySQL 驱动.
 *
 * @deprecated 3.0
 */
#[Bean(name: 'SwooleMysqlDriver')]
class Driver extends MysqlBase
{
    /**
     * 连接对象
     */
    protected ?MySQL $instance = null;

    /**
     * 最后执行过的SQL语句.
     */
    protected string $lastSql = '';

    /**
     * Statement.
     *
     * @var \Swoole\Coroutine\MySQL\Statement|array|null
     */
    protected $lastStmt = null;

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
     * 'timeout'    => '建立连接超时时间',
     * 'charset'    => '字符集',
     * 'strict_type'=> true, // 开启严格模式，query方法返回的数据也将转为强类型
     * 'fetch_mode' => false, // 开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
     * 'options'    => [], // 其它选项
     * ].
     */
    public function __construct(array $option = [])
    {
        parent::__construct($option);
        $this->isCacheStatement = Config::get('@app.db.statement.cache', true);
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(): bool
    {
        return $this->instance && $this->instance->connected;
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
        $this->instance = $instance = new MySQL();
        $option = $this->option;
        $serverConfig = [
            'host'          => $option['host'] ?? '127.0.0.1',
            'port'          => (int) ($option['port'] ?? 3306),
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
        if ($instance->connect($serverConfig))
        {
            $this->execInitSqls();

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
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
        if ($this->transaction)
        {
            $this->transaction->init();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): ?MySQL
    {
        return $this->instance;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): bool
    {
        if (!$this->inTransaction() && !$this->instance->begin())
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
        if (null === $levels || ($toLevel = $this->getTransactionLevels() - $levels) <= 0)
        {
            $result = $this->instance->rollback();
        }
        else
        {
            $this->exec('ROLLBACK TO P' . $toLevel);
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
        if ($this->lastStmt && $this->lastStmt instanceof \Swoole\Coroutine\MySQL\Statement)
        {
            return $this->lastStmt->errno;
        }
        elseif ($this->instance->connected)
        {
            return $this->instance->errno;
        }
        else
        {
            return $this->instance->connect_errno;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): string
    {
        if ($this->lastStmt && $this->lastStmt instanceof \Swoole\Coroutine\MySQL\Statement)
        {
            return $this->lastStmt->error;
        }
        elseif ($this->instance->connected)
        {
            return $this->instance->error;
        }
        else
        {
            return $this->instance->connect_error;
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
        $this->lastStmt = null;
        $this->lastSql = $sql;
        $instance = $this->instance;
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
        $result = [];
        foreach (SqlUtil::parseMultiSql($sql) as $itemSql)
        {
            $queryResult = $this->query($itemSql);
            $result[] = $queryResult->fetchAll();
        }

        return $result;
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
        return null === $this->lastStmt ? $this->instance->affected_rows : $this->lastStmt->affected_rows;
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
            $lastStmt = $this->instance->prepare($parsedSql);
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
            $this->lastStmt = $lastStmt;
            $stmt = BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql, $sqlParamsMap);
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
        $lastStmt = $this->instance->query($sql);
        if (false === $lastStmt)
        {
            $errorCode = $this->errorCode();
            $errorInfo = $this->errorInfo();
            if ($this->checkCodeIsOffline($errorCode))
            {
                $this->close();
            }
            throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }
        $this->lastStmt = $lastStmt;

        return BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction ??= new Transaction();
    }
}
