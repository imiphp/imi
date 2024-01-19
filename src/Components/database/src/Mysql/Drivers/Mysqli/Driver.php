<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Db\ConnectionCenter\DatabaseDriverConfig;
use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBase;
use Imi\Db\Mysql\Util\SqlUtil;
use Imi\Db\Transaction\Transaction;
use mysqli;

if (\extension_loaded('mysqli'))
{
    /**
     * mysqli MySQL驱动.
     */
    #[Bean(name: 'MysqliDriver')]
    class Driver extends MysqlBase
    {
        /**
         * 连接对象
         */
        protected ?\mysqli $instance = null;

        /**
         * 最后执行过的SQL语句.
         */
        protected string $lastSql = '';

        /**
         * Statement.
         */
        protected \mysqli_stmt|bool|null $lastStmt = null;

        /**
         * result.
         */
        protected \mysqli_result|bool|null $lastResult = null;

        /**
         * 事务管理.
         */
        protected ?Transaction $transaction = null;

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
            /** @var DatabaseDriverConfig $config */
            $config = $this->config;
            $this->instance = $instance = new \mysqli($config->host, $config->username ?? self::DEFAULT_USERNAME, $config->password ?? self::DEFAULT_PASSWORD, $config->database, $config->port ?? self::DEFAULT_PORT, $config->option['unix_socket'] ?? null);
            $instance->set_charset($config->charset ?? self::DEFAULT_CHARSET);
            $this->execInitSqls();

            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function close(): void
        {
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
         * @return \mysqli
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
        public function errorCode(): mixed
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

        public function insert(string $sql): int|string|null
        {
            $this->lastSql = $sql;
            $instance = $this->instance;
            $this->lastStmt = null;
            if (false === $instance->query($sql) && $this->checkCodeIsOffline($this->instance->errno))
            {
                $this->close();

                return null;
            }

            return $instance->insert_id;
        }

        public function select(string $sql): array
        {
            $this->lastSql = $sql;
            $instance = $this->instance;
            $this->lastStmt = null;
            if (false === ($result = $instance->query($sql)) && $this->checkCodeIsOffline($this->instance->errno))
            {
                $this->close();

                return [];
            }

            return $result->fetch_all(\MYSQLI_ASSOC);
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

            return BeanFactory::newInstance(Statement::class, $this, $lastStmt, null, $sql, $sqlParamsMap);
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
}
