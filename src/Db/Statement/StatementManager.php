<?php
namespace Imi\Db\Statement;

use Imi\RequestContext;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\Exception\RequestContextException;

abstract class StatementManager
{
    /**
     * statement 缓存数据
     *
     * @var array
     */
    private static $statements = [];

    /**
     * 设置statement缓存
     *
     * @param IStatement $statement
     * @param bool $using
     * @return void
     */
    public static function set(IStatement $statement, bool $using)
    {
        static::$statements[$statement->getDb()->hashCode()][$statement->getSql()] = [
            'statement'     =>  $statement,
            'using'         =>  $using,
        ];
        if($using)
        {
            try {
                RequestContext::use(function(&$context) use($statement){
                    $context['statementCaches'][] = $statement;
                });
            } catch(RequestContextException $e) {

            }
        }
    }

    /**
     * 获取连接中对应sql的statement
     * 
     * 返回数组则代表获取成功
     * 返回 null 代表没有缓存
     * 返回 false 代表当前缓存不可用
     *
     * @param IDb $db
     * @param string $sql
     * @return array|null|boolean
     */
    public static function get(IDb $db, string $sql)
    {
        $hashCode = $db->hashCode();
        $statement = &static::$statements[$hashCode][$sql] ?? null;
        if(null === $statement)
        {
            return $statement;
        }
        if($statement['using'])
        {
            return false;
        }
        $statement['using'] = true;
        try {
            RequestContext::use(function(&$context) use($statement){
                $context['statementCaches'][] = $statement['statement'];
            });
        } catch(RequestContextException $e) {

        }
        return $statement;
    }

    /**
     * 将连接中对应sql的statement设为可用
     *
     * @param IStatement $statement
     * @return void
     */
    public static function unUsing(IStatement $statement)
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        $hashCode = $db->hashCode();
        if(isset(static::$statements[$hashCode][$sql]))
        {
            $statementItem = &static::$statements[$hashCode][$sql];
            $statementItem['statement']->closeCursor();
            $statementItem['using'] = false;
            try {
                RequestContext::use(function(&$context) use($statementItem){
                    if(isset($context['statementCaches']))
                    {
                        if(false !== $i = array_search($statementItem['statement'], $context['statementCaches']))
                        {
                            unset($context['statementCaches'][$i]);
                        }
                    }
                });
            } catch(RequestContextException $e) {

            }
        }
    }

    /**
     * 将连接中所有statement设为可用
     *
     * @param IDb $db
     * @return void
     */
    public static function unUsingAll(IDb $db)
    {
        try {
            $statementCaches = RequestContext::get('statementCaches', []);
            $requestContext = true;
        } catch(RequestContextException $e) {
            $statementCaches = [];
            $requestContext = false;
        }
        foreach(static::$statements[$db->hashCode()] ?? [] as &$item)
        {
            if($requestContext && false !== $i = array_search($item['statement'], $statementCaches))
            {
                unset($statementCaches[$i]);
            }
            $item['statement']->closeCursor();
            $item['using'] = false;
        }
        if($requestContext)
        {
            RequestContext::set('statementCaches', $statementCaches);
        }
    }

    /**
     * 查询连接中有哪些sql缓存statement
     *
     * @param IDb $db
     * @return array
     */
    public static function select(IDb $db)
    {
        return static::$statements[$db->hashCode()] ?? [];
    }

    /**
     * 移除连接中对应sql的statement
     *
     * @param IStatement $statement
     * @return void
     */
    public static function remove(IStatement $statement)
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        static::unUsing($statement);
        $hashCode = $db->hashCode();
        if(isset(static::$statements[$hashCode][$sql]))
        {
            unset(static::$statements[$hashCode][$sql]);
        }
    }

    /**
     * 清空连接中所有statement
     *
     * @param IDb $db
     * @return void
     */
    public static function clear(IDb $db)
    {
        try {
            $statementCaches = RequestContext::get('statementCaches', []);
            $requestContext = true;
        } catch(RequestContextException $e) {
            $statementCaches = [];
            $requestContext = false;
        }
        $statements = static::$statements[$db->hashCode()] ?? [];
        foreach($statements as $item)
        {
            if($requestContext && false !== $i = array_search($item['statement'], $statementCaches))
            {
                unset($statementCaches[$i]);
            }
        }
        if($requestContext)
        {
            RequestContext::set('statementCaches', $statementCaches);
        }
        if($statements)
        {
            unset(static::$statements[$db->hashCode()]);
        }
    }

    /**
     * 获取所有连接及对应缓存
     *
     * @return array
     */
    public static function getAll()
    {
        return static::$statements;
    }

    /**
     * 清空所有连接及对应缓存
     *
     * @return void
     */
    public static function clearAll()
    {
        static::$statements = [];
        try {
            RequestContext::set('statementCaches', []);
        } catch(RequestContextException $e) {

        }
    }

    /**
     * 释放请求上下文
     *
     * @return void
     */
    public static function destoryRequestContext()
    {
        $statementCaches = RequestContext::get('statementCaches', []);
        foreach($statementCaches as $statement)
        {
            static::unUsing($statement);
        }
    }

}