<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\Swoole;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Config;
use Imi\Db\Exception\DbException;
use Imi\Db\Statement\StatementManager;
use Imi\Db\Transaction\Transaction;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBase;
use Imi\Pgsql\Db\Util\SqlUtil;
use Swoole\Coroutine\PostgreSQL;

if (class_exists(PostgreSQL::class, false))
{
    /**
     * Swoole Coroutine PostgreSQL 驱动.
     *
     * @Bean("SwoolePgsqlDriver")
     */
    class Driver extends PgsqlBase
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

        protected bool $connected = false;

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
         * {@inheritDoc}
         */
        public function isConnected(): bool
        {
            return $this->connected;
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
            if ($this->checkCodeIsOffline($this->errorCode()))
            {
                $this->close();
            }

            return false;
        }

        /**
         * 构建DNS字符串.
         */
        protected function buildDSN(): string
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
         * {@inheritDoc}
         */
        public function open(): bool
        {
            $this->statementIncr = 0;
            $this->instance = $instance = new PostgreSQL();

            if ($this->connected = $instance->connect($this->buildDSN()))
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
            $this->connected = false;
            StatementManager::clear($this);
            if (null !== $this->lastQueryResult)
            {
                $this->lastQueryResult = null;
            }
            if (null !== $this->instance)
            {
                $this->instance = null;
            }
            $this->transaction->init();
        }

        /**
         * {@inheritDoc}
         */
        public function getInstance(): ?PostgreSQL
        {
            return $this->instance;
        }

        /**
         * {@inheritDoc}
         */
        public function beginTransaction(): bool
        {
            if (!$this->inTransaction() && !$this->instance->query('begin'))
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return false;
            }
            $this->exec('SAVEPOINT P' . $this->getTransactionLevels());
            $this->transaction->beginTransaction();

            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function commit(): bool
        {
            if (!$this->instance->query('commit'))
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return false;
            }

            return $this->transaction->commit();
        }

        /**
         * {@inheritDoc}
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
            elseif ($this->checkCodeIsOffline($this->errorCode()))
            {
                $this->close();
            }

            return (bool) $result;
        }

        /**
         * {@inheritDoc}
         */
        public function getTransactionLevels(): int
        {
            return $this->transaction->getTransactionLevels();
        }

        /**
         * {@inheritDoc}
         */
        public function inTransaction(): bool
        {
            return $this->transaction->getTransactionLevels() > 0;
        }

        /**
         * {@inheritDoc}
         */
        public function errorCode()
        {
            if ($this->instance)
            {
                if ($this->instance->resultDiag)
                {
                    return $this->instance->resultDiag['sqlstate'] ?? null;
                }
                else
                {
                    return '';
                }
            }
            else
            {
                return null;
            }
        }

        /**
         * {@inheritDoc}
         */
        public function errorInfo(): string
        {
            return $this->instance->error ?? '';
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
            $this->lastQueryResult = $lastQueryResult = $instance->query($sql);
            if (false === $lastQueryResult)
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return 0;
            }

            return $instance->affectedRows($lastQueryResult);
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
         * {@inheritDoc}
         */
        public function rowCount(): int
        {
            return $this->instance->affectedRows($this->lastQueryResult);
        }

        /**
         * {@inheritDoc}
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
                $statementName = 'imi_stmt_' . (++$this->statementIncr);
                $this->lastQueryResult = $queryResult = $this->instance->prepare($statementName, $parsedSql);
                if (false === $queryResult)
                {
                    $errorCode = $this->errorCode();
                    $errorInfo = $this->errorInfo();
                    if ($this->checkCodeIsOffline($errorCode))
                    {
                        $this->close();
                    }
                    throw new DbException('SQL prepare error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
                $stmt = BeanFactory::newInstance(Statement::class, $this, null, $sql, $statementName, $sqlParamsMap);
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
        public function query(string $sql): IPgsqlStatement
        {
            $this->lastSql = $sql;
            $this->lastQueryResult = $queryResult = $this->instance->query($sql);
            if (false === $queryResult)
            {
                $errorCode = $this->errorCode();
                $errorInfo = $this->errorInfo();
                if ($this->checkCodeIsOffline($errorCode))
                {
                    $this->close();
                }
                throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
            }

            return BeanFactory::newInstance(Statement::class, $this, $queryResult, $sql);
        }

        /**
         * {@inheritDoc}
         */
        public function getTransaction(): Transaction
        {
            return $this->transaction;
        }
    }
}
