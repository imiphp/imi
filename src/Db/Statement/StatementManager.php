<?php

declare(strict_types=1);

namespace Imi\Db\Statement;

use Imi\Config;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Interfaces\IStatement;
use Imi\RequestContext;

class StatementManager
{
    /**
     * statement 缓存数据.
     */
    private static array $statements = [];

    /**
     * 记录每个 statement 的 sql 被使用次数.
     */
    private static array $statementSqlCount = [];

    /**
     * 每个连接最多缓存 Statement 的数量.
     */
    private static ?int $maxCacheCount = null;

    private function __construct()
    {
    }

    /**
     * 设置statement缓存.
     */
    public static function set(IStatement $statement, bool $using): void
    {
        $db = $statement->getDb();
        $hashCode = $db->hashCode();
        $sql = $statement->getSql();
        if (self::getMaxCacheCount() > 0)
        {
            self::gc($db);
            self::$statementSqlCount[$hashCode][$sql] = 1;
        }
        self::$statements[$hashCode][$sql] = [
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
     */
    public static function setNX(IStatement $statement, bool $using): bool
    {
        $db = $statement->getDb();
        $hashCode = $db->hashCode();
        $sql = $statement->getSql();
        if (isset(self::$statements[$hashCode][$sql]))
        {
            return false;
        }
        if (self::getMaxCacheCount() > 0)
        {
            self::gc($db = $statement->getDb());
            self::$statementSqlCount[$hashCode][$sql] = 1;
        }
        self::$statements[$hashCode][$sql] = [
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

    public static function getMaxCacheCount(): int
    {
        return self::$maxCacheCount ??= (int) Config::get('@app.db.statement.maxCacheCount', 0);
    }

    public static function gc(IDb $db): int
    {
        $cacheCount = self::getMaxCacheCount();
        if ($cacheCount <= 0)
        {
            return 0;
        }
        $hashCode = $db->hashCode();
        $staticStatements = &self::$statements;
        if (!isset($staticStatements[$hashCode]))
        {
            return 0;
        }
        $maxGcCount = \count($staticStatements[$hashCode]) - $cacheCount;
        if ($maxGcCount <= 0)
        {
            return 0;
        }
        asort(self::$statementSqlCount);
        $gcCount = 0;
        foreach (self::$statementSqlCount[$hashCode] as $sql => $count)
        {
            if (!$staticStatements[$hashCode][$sql]['using'])
            {
                unset($staticStatements[$hashCode][$sql], self::$statementSqlCount[$hashCode][$sql]);
            }
            if (++$gcCount === $maxGcCount)
            {
                break;
            }
        }

        return $gcCount;
    }

    /**
     * 获取连接中对应sql的statement.
     *
     * 返回数组则代表获取成功
     * 返回 null 代表没有缓存
     * 返回 false 代表当前缓存不可用
     *
     * @return array|bool|null
     */
    public static function get(IDb $db, string $sql)
    {
        $hashCode = $db->hashCode();
        if (!isset(self::$statements[$hashCode][$sql]))
        {
            return null;
        }
        $statement = &self::$statements[$hashCode][$sql];
        if ($statement['using'])
        {
            return false;
        }
        $statement['using'] = true;
        $context = RequestContext::getContext();
        $context['statementCaches'][] = $statement['statement'];
        if (self::getMaxCacheCount() > 0)
        {
            ++self::$statementSqlCount[$hashCode][$sql];
        }

        return $statement;
    }

    /**
     * 将连接中对应sql的statement设为可用.
     */
    public static function unUsing(IStatement $statement): void
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        $hashCode = $db->hashCode();
        $staticStatements = &self::$statements;
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
     */
    public static function unUsingAll(IDb $db): void
    {
        $context = RequestContext::getContext();
        $statements = self::$statements[$db->hashCode()] ?? [];
        if ($statements)
        {
            $statementCaches = $context['statementCaches'] ?? [];
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
            $context['statementCaches'] = $statementCaches;
        }
    }

    /**
     * 查询连接中有哪些sql缓存statement.
     */
    public static function select(IDb $db): array
    {
        return self::$statements[$db->hashCode()] ?? [];
    }

    /**
     * 移除连接中对应sql的statement.
     */
    public static function remove(IStatement $statement): void
    {
        $db = $statement->getDb();
        $sql = $statement->getSql();
        static::unUsing($statement);
        $hashCode = $db->hashCode();
        $staticStatements = &self::$statements;
        if (isset($staticStatements[$hashCode][$sql]))
        {
            unset($staticStatements[$hashCode][$sql]);
        }
    }

    /**
     * 清空连接中所有statement.
     */
    public static function clear(IDb $db): void
    {
        $requestContext = RequestContext::getContext();
        $staticStatements = &self::$statements;
        $statements = $staticStatements[$db->hashCode()] ?? [];
        if ($statements)
        {
            $statementCaches = $requestContext['statementCaches'] ?? [];
            foreach ($statements as $item)
            {
                if (false !== $i = array_search($item['statement'], $statementCaches))
                {
                    unset($statementCaches[$i]);
                }
            }
            unset($staticStatements[$db->hashCode()]);
            $requestContext['statementCaches'] = $statementCaches;
        }
    }

    /**
     * 获取所有连接及对应缓存.
     */
    public static function getAll(): array
    {
        return self::$statements;
    }

    /**
     * 清空所有连接及对应缓存.
     */
    public static function clearAll(): void
    {
        self::$statements = [];
        RequestContext::set('statementCaches', []);
    }
}
