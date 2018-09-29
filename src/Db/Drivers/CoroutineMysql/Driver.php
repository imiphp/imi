<?php

namespace Imi\Db\Drivers\CoroutineMysql;

use Imi\App;
use Imi\Db\Drivers\Base;
use Imi\Bean\BeanFactory;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Traits\SqlParser;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IStatement;
use Imi\Pool\Interfaces\IPoolResource;

/**
 * Swoole协程MySQL驱动
 */
class Driver extends Base implements IDb
{
    use SqlParser;

    /**
     * 连接对象
     * @var \Swoole\Coroutine\MySQL
     */
    protected $instance;

    /**
     * 连接配置
     * @var array
     */
    protected $option;

    /**
     * 是否在一个事务内
     * @var boolean
     */
    protected $inTransaction = false;

    /**
     * 最后执行过的SQL语句
     * @var string
     */
    protected $lastSql = '';

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
     * 'strict_type' => false, //开启严格模式，返回的字段将自动转为数字类型
     * ]
     *
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->option = $option;
        if(isset($this->option['username']))
        {
            $this->option['user'] = $this->option['username'];
        }
        else
        {
            $this->option['user'] = 'root';
        }
        if(!isset($option['password']))
        {
            $this->option['password'] = '';
        }
        $this->instance = new \Swoole\Coroutine\MySQL();
    }

    /**
     * 打开
     * @return boolean
     */
    public function open()
    {
        return $this->instance->connect($this->option);
    }

    /**
     * 关闭
     * @return void
     */
    public function close()
    {
        $this->instance->close();
    }

    /**
     * 是否已连接
     * @return boolean
     */
    public function isConnected(): bool
    {
        return $this->instance->connected;
    }

    /**
     * 获取原对象实例
     * @return \Swoole\Coroutine\MySQL
     */
    public function getInstance(): \Swoole\Coroutine\MySQL
    {
        return $this->instance;
    }

    protected function __beginTransaction()
    {
        $result = $this->instance->begin();
        yield $result;
        $result = yield;
        if($result)
        {
            $this->inTransaction = true;
        }
        return $result;
    }

    /**
     * 启动一个事务
     * @return boolean
     */
    public function beginTransaction(): bool
    {
        $generator = $this->__beginTransaction();
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
    }

    protected function __commit()
    {
        $this->inTransaction = false;
        $result = $this->instance->commit();
        yield $result;
        $result = yield;
        return $result;
    }

    /**
     * 提交一个事务
     * @return boolean
     */
    public function commit(): bool
    {
        $generator = $this->__commit();
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
    }

    protected function __rollBack()
    {
        $this->inTransaction = false;
        $result = $this->instance->rollback();
        yield $result;
        $result = yield;
        return $result;
    }

    /**
     * 回滚一个事务
     * @return boolean
     */
    public function rollBack(): bool
    {
        $generator = $this->__rollBack();
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
    }

    /**
     * 检查是否在一个事务内
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * 返回错误码
     * @return mixed
     */
    public function errorCode()
    {
        return $this->instance->errno;
    }

    /**
     * 返回错误信息
     * @return array
     */
    public function errorInfo(): string
    {
        return $this->instance->error;
    }

    /**
     * 获取最后一条执行的SQL语句
     * @return string
     */
    public function lastSql()
    {
        return $this->lastSql;
    }

    protected function __exec(string $sql)
    {
        $result = $this->instance->query($sql);
        yield $result;
        $result = yield;
        if(false === $result)
        {
            return 0;
        }
        else
        {
            return $this->instance->affected_rows;
        }
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
        $generator = $this->__exec($sql);
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
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
        return null;
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
        return true;
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
        return $this->instance->insert_id;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数
     * @return int
     */
    public function rowCount(): int
    {
        return $this->instance->affected_rows;
    }

    protected function __prepare(string $sql, array $driverOptions = [])
    {
        // 处理支持 :xxx 参数格式
        $this->lastSql = $sql;
        $execSql = $this->parseSqlNameParamsToQuestionMark($sql, $params);
        $stmt = $this->instance->prepare($execSql);
        yield $stmt;
        $stmt = yield;
        if(false === $stmt)
        {
            throw new DbException('sql prepare error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $sql);
        }
        return BeanFactory::newInstance(Statement::class, $this, $stmt, $sql, $params);
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
        $generator = $this->__prepare($sql, $driverOptions);
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
    }

    protected function __query(string $sql)
    {
        $this->lastSql = $sql;
        $stmt = $this->instance->query($sql);
        yield $stmt;
        $stmt = yield;
        if(false === $stmt)
        {
            throw new DbException('sql query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $sql);
        }
        $data = $stmt->execute([]);
        return BeanFactory::newInstance(Statement::class, $this, $stmt, $sql, [], $data);
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
        $generator = $this->__query($sql);
        if(!$generator->valid())
        {
            return $generator->getReturn();
        }
        $current = $generator->current();
        $generator->next();
        $generator->send($current);
        return $generator->getReturn();
    }
}