<?php

namespace Imi\Db\Statement;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\RequestContext;

abstract class StatementManager
{
    /**
     * statement 缓存数据.
     *
     * @var array
     */
    private static $statements = [];

    /**
     * 设置statement缓存.
     *
     * @param IStatement $statement
     * @param bool       $using
     *
     * @return void
     */
    public static function set(IStatement $statement, bool $using)
    {
        static::$statements[$statement->getDb()->hashCode()][$statement->getSql()] = [
            'statement'     => $statement,
            'using'         => $using,
        ];
        if ($using)
        {
            $context = RequestContext::getContext();
            $context['statementCaches'][] = $statement;
        }
    }

    /**
     * 设置statement缓存，存在则不设置.
     *
     * @param IStatement $statement
     * @param bool       $using
     *
     * @return bool
     */
    public static function setNX(IStatement $statement, bool $using)
    {
        $hashCode = $statement->getDb()->hashCode();
        $sql = $statement->getSql();
        if (isset(static::$statements[$hashCode][$sql]))
        {
            return false;
        }
        static::$statements[$hashCode][$sql] = [
            'statement'     => $statement,
            'using'         => $using,
        ];
        if ($using)
        {
            $context = RequestContext::getContext();
            $context['statementCaches'][] = $statement;
        }

        return true;
    }

    /**
     * 获取连接中对应sql的statement.
     *
     * 返回数组则代表获取成功
     * 返回 null 代表没有缓存
     * 返回 false 代表当前缓存不可用
     *
     * @param IDb    $db
     * @param string $sql
     *
     * @return array|bool|null
     */
    public static function get(IDb $db, string $sql)
    {
        $hashCode = $db->hashCode();
        $statement = &static::$statements[$hashCode][$sql] ?? null;
        if (null === $statement)
        {
            return $statement;
        }
        if ($statement['using'])
        {
            return false;
        }
        $statement['using'] = true;
        $context = RequestContext::getContext();
        $context['statementCaches'][] = $statement['statement'];

        return $statement;
    }

    /**
     * 将连接中对应sql的statement设为可用.
     *
     * @param IStatement $statement
     *
     * @return void
     */
    public static function unUsing(IStatement $statement)
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        $hashCode = $db->hashCode();
        $staticStatements = &static::$statements;
        if (isset($staticStatements[$hashCode][$sql]))
        {
            $statementItem = &$staticStatements[$hashCode][$sql];
            $statement = $statementItem['statement'];
            if ($statement)
            {
                $statement->closeCursor();
            }
            $statementItem['using'] = false;
            $context = RequestContext::getContext();
            if (isset($context['statementCaches']))
            {
                if (false !== $i = array_search($statement, $context['statementCaches']))
                {
                    unset($context['statementCaches'][$i]);
                }
            }
        }
    }

    /**
     * 将连接中所有statement设为可用.
     *
     * @param IDb $db
     *
     * @return void
     */
    public static function unUsingAll(IDb $db)
    {
        $context = RequestContext::getContext();
        $statementCaches = $context['statementCaches'] ?? [];
        $statements = static::$statements[$db->hashCode()] ?? [];
        if ($statements)
        {
            foreach ($statements as &$item)
            {
                $statement = $item['statement'];
                if (false !== $i = array_search($statement, $statementCaches))
                {
                    unset($statementCaches[$i]);
                }
                if ($statement)
                {
                    $statement->closeCursor();
                }
                $item['using'] = false;
            }
        }
        $context['statementCaches'] = $statementCaches;
    }

    /**
     * 查询连接中有哪些sql缓存statement.
     *
     * @param IDb $db
     *
     * @return array
     */
    public static function select(IDb $db)
    {
        return static::$statements[$db->hashCode()] ?? [];
    }

    /**
     * 移除连接中对应sql的statement.
     *
     * @param IStatement $statement
     *
     * @return void
     */
    public static function remove(IStatement $statement)
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        static::unUsing($statement);
        $hashCode = $db->hashCode();
        $staticStatements = &static::$statements;
        if (isset($staticStatements[$hashCode][$sql]))
        {
            unset($staticStatements[$hashCode][$sql]);
        }
    }

    /**
     * 清空连接中所有statement.
     *
     * @param IDb $db
     *
     * @return void
     */
    public static function clear(IDb $db)
    {
        $requestContext = RequestContext::getContext();
        $statementCaches = $requestContext['statementCaches'] ?? [];
        $staticStatements = &static::$statements;
        $statements = $staticStatements[$db->hashCode()] ?? [];
        if ($statements)
        {
            foreach ($statements as $item)
            {
                if (false !== $i = array_search($item['statement'], $statementCaches))
                {
                    unset($statementCaches[$i]);
                }
            }
            unset($staticStatements[$db->hashCode()]);
        }
        $requestContext['statementCaches'] = $statementCaches;
    }

    /**
     * 获取所有连接及对应缓存.
     *
     * @return array
     */
    public static function getAll()
    {
        return static::$statements;
    }

    /**
     * 清空所有连接及对应缓存.
     *
     * @return void
     */
    public static function clearAll()
    {
        static::$statements = [];
        RequestContext::set('statementCaches', []);
    }
}
