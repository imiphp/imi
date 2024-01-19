<?php

declare(strict_types=1);

namespace Imi\Db\Drivers\PDO;

use Imi\Bean\BeanFactory;
use Imi\Db\ConnectionCenter\DatabaseDriverConfig;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Transaction\Transaction;

if (\extension_loaded('pdo'))
{
    trait TPDODriver
    {
        /**
         * 连接对象
         */
        protected ?\PDO $instance = null;

        /**
         * 最后执行过的SQL语句.
         */
        protected string $lastSql = '';

        /**
         * Statement.
         */
        protected \PDOStatement|bool|null $lastStmt = null;

        /**
         * 事务管理.
         */
        protected ?Transaction $transaction = null;

        /**
         * Statement 类名.
         */
        protected string $statementClass = '';

        /**
         * 构建DNS字符串.
         */
        abstract protected function buildDSN(): string;

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
            try
            {
                if ($instance->query('select 1'))
                {
                    return true;
                }
                // @phpstan-ignore-next-line
                if ($this->checkCodeIsOffline($instance->errorCode()))
                {
                    $this->close();
                }
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                else
                {
                    throw $e;
                }
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
            $this->instance = new \PDO($this->buildDSN(), $config->username ?? self::DEFAULT_USERNAME, $config->password ?? self::DEFAULT_PASSWORD, ($config->option['options'] ?? []) + self::DEFAULT_OPTIONS);
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
                $this->lastStmt = null;
            }
            $this->instance = null;
            if ($this->transaction)
            {
                $this->transaction->init();
            }
        }

        /**
         * @return \PDO
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
            try
            {
                if (!$this->inTransaction() && !$this->instance->beginTransaction())
                {
                    // @phpstan-ignore-next-line
                    if ($this->checkCodeIsOffline($this->instance->errorCode()))
                    {
                        $this->close();
                    }

                    return false;
                }
                $this->exec('SAVEPOINT P' . $this->getTransactionLevels());
                $this->getTransaction()->beginTransaction();
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function commit(): bool
        {
            try
            {
                if (!$this->instance->commit())
                {
                    // @phpstan-ignore-next-line
                    if ($this->checkCodeIsOffline($this->instance->errorCode()))
                    {
                        $this->close();
                    }

                    return false;
                }
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
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
                try
                {
                    $result = $this->instance->rollback();
                }
                catch (\PDOException $e)
                {
                    if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                    {
                        $this->close();
                    }
                    throw $e;
                }
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
            // @phpstan-ignore-next-line
            elseif ($this->checkCodeIsOffline($this->instance->errorCode()))
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
            return $this->instance->inTransaction();
        }

        /**
         * {@inheritDoc}
         */
        public function errorCode(): mixed
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
         * {@inheritDoc}
         */
        public function errorInfo(): string
        {
            if ($this->lastStmt)
            {
                $errorInfo = $this->lastStmt->errorInfo();
            }
            else
            {
                $errorInfo = $this->instance->errorInfo();
            }
            if (null === $errorInfo[1] && null === $errorInfo[2])
            {
                return '';
            }

            return $errorInfo[1] . ':' . $errorInfo[2];
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
            $this->lastStmt = null;

            try
            {
                $result = $this->instance->exec($sql);
                if (false === $result)
                {
                    $errorCode = $this->errorCode();
                    $errorInfo = $this->errorInfo();
                    if ($this->checkCodeIsOffline($errorCode))
                    {
                        $this->close();
                    }
                    throw new DbException('SQL exec error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return $result;
        }

        public function insert(string $sql): int|string|null
        {
            $this->lastSql = $sql;
            $this->lastStmt = null;

            try
            {
                $result = $this->instance->exec($sql);
                if (false === $result)
                {
                    $errorCode = $this->errorCode();
                    $errorInfo = $this->errorInfo();
                    if ($this->checkCodeIsOffline($errorCode))
                    {
                        $this->close();
                    }
                    throw new DbException('SQL exec error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return $this->instance->lastInsertId();
        }

        public function select(string $sql): array
        {
            try
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
                    throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
                $this->lastStmt = $lastStmt;
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return $lastStmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * {@inheritDoc}
         */
        public function getAttribute(mixed $attribute): mixed
        {
            return $this->instance->getAttribute($attribute);
        }

        /**
         * {@inheritDoc}
         */
        public function setAttribute(mixed $attribute, mixed $value): bool
        {
            return $this->instance->setAttribute($attribute, $value);
        }

        /**
         * {@inheritDoc}
         */
        public function lastInsertId(?string $name = null): string
        {
            return $this->instance->lastInsertId($name);
        }

        /**
         * {@inheritDoc}
         */
        public function rowCount(): int
        {
            return null === $this->lastStmt ? 0 : $this->lastStmt->rowCount();
        }

        /**
         * {@inheritDoc}
         */
        public function prepare(string $sql, array $driverOptions = []): IStatement
        {
            try
            {
                $this->lastSql = $sql;
                $lastStmt = $this->instance->prepare($sql, $driverOptions);
                // @phpstan-ignore-next-line
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
                $stmt = BeanFactory::newInstance($this->statementClass, $this, $lastStmt);
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return $stmt;
        }

        /**
         * {@inheritDoc}
         */
        public function query(string $sql): IStatement
        {
            try
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
                    throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
                }
                $this->lastStmt = $lastStmt;
            }
            catch (\PDOException $e)
            {
                if (isset($e->errorInfo[0]) && $this->checkCodeIsOffline($e->errorInfo[0]))
                {
                    $this->close();
                }
                throw $e;
            }

            return BeanFactory::newInstance($this->statementClass, $this, $lastStmt, true);
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
