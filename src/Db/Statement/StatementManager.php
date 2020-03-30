<?php
namespace Imi\Db\Statement;

use Imi\RequestContext;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;

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
            $context = RequestContext::getContext();
            $context['statementCaches'][] = $statement;
        }
    }

    /**
     * 设置statement缓存，存在则不设置
     *
     * @param IStatement $statement
     * @param bool $using
     * @return bool
     */
    public static function setNX(IStatement $statement, bool $using)
    {
        $hashCode = $statement->getDb()->hashCode();
        $sql = $statement->getSql();
        if(isset(static::$statements[$hashCode][$sql]))
        {
            return false;
        }
        static::$statements[$hashCode][$sql] = [
            'statement'     =>  $statement,
            'using'         =>  $using,
        ];
        if($using)
        {
            $context = RequestContext::getContext();
            $context['statementCaches'][] = $statement;
        }
        return true;
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
        $context = RequestContext::getContext();
        $context['statementCaches'][] = $statement['statement'];
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
            if($statementItem['statement'])
            {
                $statementItem['statement']->closeCursor();
            }
            $statementItem['using'] = false;
            $context = RequestContext::getContext();
            if(isset($context['statementCaches']))
            {
                if(false !== $i = array_search($statementItem['statement'], $context['statementCaches']))
                {
                    unset($context['statementCaches'][$i]);
                }
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
        $context = RequestContext::getContext();
        $statementCaches = $context['statementCaches'] ?? [];
        $requestContext = true;
        foreach(static::$statements[$db->hashCode()] ?? [] as &$item)
        {
            if($requestContext && false !== $i = array_search($item['statement'], $statementCaches))
            {
                unset($statementCaches[$i]);
            }
            if($item['statement'])
            {
                $item['statement']->closeCursor();
            }
            $item['using'] = false;
        }
        if($requestContext)
        {
            $context['statementCaches'] = $statementCaches;
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
        $requestContext = RequestContext::getContext();
        $statementCaches = $requestContext['statementCaches'] ?? [];
        $isRequestContext = true;
        $statements = static::$statements[$db->hashCode()] ?? [];
        foreach($statements as $item)
        {
            if($isRequestContext && false !== $i = array_search($item['statement'], $statementCaches))
            {
                unset($statementCaches[$i]);
            }
        }
        if($isRequestContext)
        {
            $requestContext['statementCaches'] = $statementCaches;
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
        RequestContext::set('statementCaches', []);
    }

}