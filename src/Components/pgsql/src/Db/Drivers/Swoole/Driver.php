<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\Swoole;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Db\Exception\DbException;
use Imi\Db\Transaction\Transaction;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBase;
use Imi\Pgsql\Db\Util\SqlUtil;
use Swoole\Coroutine\PostgreSQL;
use Swoole\Coroutine\PostgreSQLStatement;

if (class_exists(PostgreSQL::class, false))
{
    /**
     * Swoole Coroutine PostgreSQL 驱动.
     */
    #[Bean(name: 'SwoolePgsqlDriver')]
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
         * Statement.
         */
        protected ?PostgreSQLStatement $lastStmt = null;

        /**
         * 事务管理.
         */
        protected ?Transaction $transaction = null;

        protected bool $connected = false;

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
            $config = $this->config;
            if (null !== $config->dsn)
            {
                return $config->dsn;
            }
            $otherOptionsContent = '';
            if (isset($config->option['options']))
            {
                foreach ($config->option['options'] as $k => $v)
                {
                    $otherOptionsContent .= ' ' . $k . '=' . $v;
                }
            }

            return 'host=' . $config->host
                    . ' port=' . ($config->port ?? self::DEFAULT_PORT)
                    . ' dbname=' . ($config->database ?? '')
                    . ' user=' . ($config->username ?? self::DEFAULT_USERNAME)
                    . ' password=' . ($config->password ?? self::DEFAULT_PASSWORD)
                    . $otherOptionsContent
            ;
        }

        /**
         * {@inheritDoc}
         */
        public function open(): bool
        {
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
            if (null !== $this->lastStmt)
            {
                $this->lastStmt = null;
            }
            if (null !== $this->instance)
            {
                $this->instance = null;
            }
            if ($this->transaction)
            {
                $this->transaction->init();
            }
        }

        /**
         * @return PostgreSQL
         */
        public function getInstance(): object
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
            $this->getTransaction()->beginTransaction();

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

            return $this->getTransaction()->commit();
        }

        /**
         * {@inheritDoc}
         */
        public function rollBack(?int $levels = null): bool
        {
            if (null === $levels || ($toLevel = $this->getTransactionLevels() - $levels) <= 0)
            {
                $result = $this->instance->query('rollback');
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
        public function errorCode(): mixed
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
            $lastStmt = $instance->query($sql);
            if (false === $lastStmt)
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return 0;
            }
            $this->lastStmt = $lastStmt;

            return $lastStmt->affectedRows();
        }

        public function insert(string $sql): int|string|null
        {
            $this->lastSql = $sql;
            $instance = $this->instance;
            $lastStmt = $instance->query($sql);
            if (false === $lastStmt)
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return null;
            }
            $this->lastStmt = $lastStmt;

            return $this->lastInsertId();
        }

        public function select(string $sql): array
        {
            $this->lastSql = $sql;
            $instance = $this->instance;
            $lastStmt = $instance->query($sql);
            if (false === $lastStmt)
            {
                if ($this->checkCodeIsOffline($this->errorCode()))
                {
                    $this->close();
                }

                return [];
            }
            $this->lastStmt = $lastStmt;

            return $lastStmt->fetchAll();
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
        public function getAttribute(mixed $attribute): mixed
        {
            return null;
        }

        /**
         * {@inheritDoc}
         */
        public function setAttribute(mixed $attribute, mixed $value): bool
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
                $lastStmt = $this->instance->query($sql = 'select LASTVAL()');
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
                $row = $lastStmt->fetchRow(0);

                return (string) reset($row);
            }
            else
            {
                $lastStmt = $this->instance->prepare($sql = 'SELECT CURRVAL($1)');
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
                if (false === $lastStmt->execute([$name]))
                {
                    $errorCode = $lastStmt->resultDiag['sqlstate'] ?? '';
                    $errorInfo = $lastStmt->error ?? '';
                    if ($this->checkCodeIsOffline($errorCode))
                    {
                        $this->close();
                    }
                    throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
                $row = $lastStmt->fetchRow(0);

                return (string) reset($row);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function rowCount(): int
        {
            return $this->lastStmt->affectedRows();
        }

        /**
         * {@inheritDoc}
         */
        public function prepare(string $sql, array $driverOptions = []): IPgsqlStatement
        {
            $this->lastSql = $sql;
            $parsedSql = SqlUtil::parseSqlWithParams($sql, $sqlParamsMap);
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

            return BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql, $sqlParamsMap);
        }

        /**
         * {@inheritDoc}
         */
        public function query(string $sql): IPgsqlStatement
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

            return BeanFactory::newInstance(Statement::class, $this, $lastStmt, $sql, null, true);
        }

        /**
         * {@inheritDoc}
         */
        public function getTransaction(): Transaction
        {
            return $this->transaction ??= new Transaction();
        }
    }
}
