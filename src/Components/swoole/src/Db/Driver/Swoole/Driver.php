<?php

declare(strict_types=1);

namespace Imi\Swoole\Db\Driver\Swoole;

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
use Swoole\Coroutine\MySQL;

/**
 * Swoole Coroutine MySQL 驱动.
 *
 * @Bean("SwooleMysqlDriver")
 */
class Driver extends MysqlBase implements IMysqlDb
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
    protected Transaction $transaction;

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
        $this->transaction = new Transaction();
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        $instance = $this->instance;

        return $instance && $instance->query('select 1');
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $this->instance = $instance = new MySQL();
        $option = $this->option;
        $serverConfig = [
            'host'          => $option['host'] ?? '127.0.0.1',
            'port'          => $option['port'] ?? 3306,
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
        $result = $instance->connect($serverConfig);
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
        if (null !== $this->lastStmt)
        {
            $this->lastStmt = null;
        }
        if (null !== $this->instance)
        {
            $this->instance->close();
            $this->instance = null;
        }
    }

    /**
     * 获取原对象实例.
     */
    public function getInstance(): MySQL
    {
        return $this->instance;
    }

    /**
     * 启动一个事务
     */
    public function beginTransaction(): bool
    {
        if (!$this->inTransaction() && !$this->instance->begin())
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
        $this->lastStmt = null;
        $this->lastSql = $sql;
        $instance = $this->instance;
        $instance->query($sql);

        return $instance->affected_rows;
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
        return (string) $this->instance->insert_id;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return null === $this->lastStmt ? $this->instance->affected_rows : $this->lastStmt->affected_rows;
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
            $stmt = App::getBean(Statement::class, $this, $lastStmt, $sql, $sqlParamsMap);
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
        $this->lastStmt = $lastStmt = $this->instance->query($sql);
        if (false === $lastStmt)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . \PHP_EOL . 'sql: ' . $sql . \PHP_EOL);
        }

        return App::getBean(Statement::class, $this, $lastStmt, $sql);
    }

    /**
     * Get 事务管理.
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
