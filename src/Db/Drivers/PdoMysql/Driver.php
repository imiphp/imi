<?php

namespace Imi\Db\Drivers\PdoMysql;

use Imi\Db\Drivers\Base;
use Imi\Bean\BeanFactory;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Traits\SqlParser;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IStatement;

/**
 * PDO MySQL驱动
 */
class Driver extends Base implements IDb
{
    use SqlParser;

    /**
     * 连接对象
     * @var \PDO
     */
    protected $instance;

    /**
     * 连接配置
     * @var array
     */
    protected $option;

    /**
     * 最后执行过的SQL语句
     * @var string
     */
    protected $lastSql = '';

    /**
     * Statement
     * @var Statement
     */
    protected $lastStmt;

    /**
     * 参数格式：
     * [
     * 'host' => 'MySQL IP地址',
     * 'username' => '数据用户',
     * 'password' => '数据库密码',
     * 'database' => '数据库名',
     * 'port'    => 'MySQL端口 默认3306 可选参数',
     * 'timeout' => '建立连接超时时间',
     * 'charset' => '字符集',
     * 'options' => [], // PDO连接选项
     * ]
     *
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->option = $option;
        if(!isset($this->option['username'])) {
            $this->option['username'] = 'root';
        }
        if(!isset($option['password'])) {
            $this->option['password'] = '';
        }
        if(!isset($option['options'])) {
            $this->option['options'] = [];
        }
    }

    /**
     * 构建DNS字符串
     * @return string
     */
    protected function buildDSN()
    {
        if(isset($this->option['dsn'])) {
            return $this->option['dsn'];
        }
        return 'mysql:'
            . 'host=' . ($this->option['host'] ?? '127.0.0.1')
            . ';port=' . ($this->option['port'] ?? '3306')
            . ';dbname=' . ($this->option['database'] ?? '')
            . ';unix_socket=' . ($this->option['unix_socket'] ?? '')
            . ';charset=' . ($this->option['charset'] ?? 'utf8');
    }

    /**
     * 是否已连接
     * @return boolean
     */
    public function isConnected(): bool
    {
        try {
            $this->instance->getAttribute(\PDO::ATTR_SERVER_INFO);
        } catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * 打开
     * @return boolean
     */
    public function open()
    {
        $this->instance = new \PDO($this->buildDSN(), $this->option['username'], $this->option['password'], $this->option['options']);
        return true;
    }

    /**
     * 关闭
     * @return void
     */
    public function close()
    {
        $this->instance = null;
    }

    /**
     * 获取原对象实例
     * @return \PDO
     */
    public function getInstance(): \PDO
    {
        return $this->instance;
    }

    /**
     * 启动一个事务
     * @return boolean
     */
    public function beginTransaction(): bool
    {
        return $this->instance->beginTransaction();
    }

    /**
     * 提交一个事务
     * @return boolean
     */
    public function commit(): bool
    {
        return $this->instance->commit();
    }

    /**
     * 回滚一个事务
     * @return boolean
     */
    public function rollBack(): bool
    {
        return $this->instance->rollback();
    }

    /**
     * 检查是否在一个事务内
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->instance->inTransaction();
    }

    /**
     * 返回错误码
     * @return mixed
     */
    public function errorCode()
    {
        if($this->lastStmt) {
            return $this->lastStmt->errorCode();
        } else {
            return $this->instance->errorCode();
        }
    }

    /**
     * 返回错误信息
     * @return array
     */
    public function errorInfo(): string
    {
        if($this->lastStmt) {
            return $this->lastStmt->errorInfo();
        } else {
            $errorInfo = $this->instance->errorInfo();
            return !isset($errorInfo[0]) || 0 == $errorInfo[0] ? '' : implode(' ', $errorInfo);
        }
    }

    /**
     * 获取最后一条执行的SQL语句
     * @return string
     */
    public function lastSql()
    {
        return $this->lastSql;
    }

    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     *
     * @param string $sql
     *
     * @return integer
     */
    public function exec(string $sql): int
    {
        return $this->instance->exec($sql);
    }

    /**
     * 取回一个数据库连接的属性
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
     * 设置属性
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
     * 返回受上一个 SQL 语句影响的行数
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
     * @throws DbException
     */
    public function prepare(string $sql, array $driverOptions = [])
    {
        // 处理支持 :xxx 参数格式
        $this->lastSql = $sql;
        $this->lastStmt = $this->instance->prepare($sql, $driverOptions);
        if(false === $this->lastStmt) {
            throw new DbException('sql prepare error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $sql);
        }
        return BeanFactory::newInstance(Statement::class, $this, $this->lastStmt);
    }

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     *
     * @param string $sql
     *
     * @return IStatement
     * @throws DbException
     */
    public function query(string $sql)
    {
        $this->lastSql = $sql;
        $this->lastStmt = $this->instance->query($sql);
        if(false === $this->lastStmt) {
            throw new DbException('sql prepare error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $sql);
        }
        return BeanFactory::newInstance(Statement::class, $this, $this->lastStmt);
    }
}