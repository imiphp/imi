<?php
namespace Imi\Db\Drivers;

use Imi\Db\Interfaces\IDb;
use Imi\Util\Defer;

abstract class Base implements IDb
{
    /**
     * 启动一个事务
     * @return Defer
     */
    public function deferBeginTransaction(): Defer
    {
        return $this->parseDefer('beginTransaction');
    }

    /**
     * 提交一个事务
     * @return Defer
     */
    public function deferCommit(): Defer
    {
        return $this->parseDefer('commit');
    }

    /**
     * 回滚一个事务
     * @return Defer
     */
    public function deferRollBack(): Defer
    {
        return $this->parseDefer('rollback');
    }

    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     * @param string $sql
     * @return Defer
     */
    public function deferExec(string $sql): Defer
    {
        return $this->parseDefer('exec', $sql);
    }

    /**
     * 准备执行语句并返回一个语句对象
     * @param string $sql
     * @param array $driverOptions
     * @return Defer
     */
    public function deferPrepare(string $sql, array $driverOptions = []): Defer
    {
        return $this->parseDefer('prepare', $sql, $driverOptions);
    }

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     * @param string $sql
     * @return Defer
     */
    public function deferQuery(string $sql): Defer
    {
        return $this->parseDefer('query', $sql);
    }

    /**
     * 处理延迟调用
     *
     * @param string $methodName
     * @param array $args
     * @return Defer
     */
    protected function parseDefer($methodName, ...$args)
    {
        $innerMethodName = '__' . $methodName;
        $instance = $this->getInstance();
        if(method_exists($instance, 'setDefer'))
        {
            $instance->setDefer(true);
            $generate = $this->$innerMethodName(...$args);
            $generate->next();
            $callable = function() use($instance, $generate){
                $result = $instance->recv();
                $generate->send($result);
                return $generate->getReturn();
            };
        }
        else
        {
            $callable = function(){
                return $this->$methodName(...$args);
            };
        }
        return new Defer($callable);
    }
}