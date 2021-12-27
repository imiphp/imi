<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Util;

use Imi\Db\Exception\DbException;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Table;

/**
 * SQL 工具类.
 */
class SqlUtil
{
    private function __construct()
    {
    }

    /**
     * 解析带参数的 SQL，返回解析后的 SQL.
     *
     * 支持 :xxx、?
     *
     * @param array $map
     */
    public static function parseSqlWithParams(string $sql, ?array &$map): string
    {
        $map = [];

        $i = 0;

        return preg_replace_callback('/([^:])(:([a-zA-Z0-9_]+)|\?)/', function (array $match) use (&$map, &$i): string {
            $map[] = $match[3] ?? $match[1];

            return $match[1] . '$' . (++$i);
        }, $sql);
    }

    /**
     * 处理多行 SQL 为一个数组.
     */
    public static function parseMultiSql(string $sql): array
    {
        $result = [];
        $begin = 0;
        $i = 0;
        $sqlLength = \strlen($sql);
        // 关闭单引号
        $closeApostrophe = true;
        for ($i = 0; $i < $sqlLength; ++$i)
        {
            switch ($sql[$i])
            {
                case ';':
                    if ($closeApostrophe)
                    {
                        $sqlString = trim(substr($sql, $begin, $i + 1 - $begin));
                        $begin = $i + 1;
                        if ('' !== $sqlString && ';' !== $sqlString)
                        {
                            $result[] = $sqlString;
                        }
                    }
                    break;
                case '\\':
                    $next = $i + 1;
                    if (isset($sql[$next]) && '\'' === $sql[$next])
                    {
                        // 下个字符是单引号，算转义，跳过
                        $i = $next;
                    }
                    break;
                case '\'':
                    $next = $i + 1;
                    if (isset($sql[$next]) && '\'' === $sql[$next])
                    {
                        // 下个字符是单引号，算转义，跳过
                        $i = $next;
                    }
                    else
                    {
                        $closeApostrophe = !$closeApostrophe;
                    }
                    break;
            }
        }
        $leftSql = substr($sql, $begin, $i + 1 - $begin);
        if ('' !== trim($leftSql))
        {
            throw new DbException(sprintf('Invalid sql: %s', $leftSql));
        }

        return $result;
    }

    /**
     * 生成插入语句.
     */
    public static function buildInsertSql(IQuery $query, string $table, array $dataList): string
    {
        $sql = '';
        $tableObj = new Table();
        $tableObj->setValue($table, $query);
        $tableStr = $tableObj->toString($query);
        foreach ($dataList as $row)
        {
            foreach ($row as &$value)
            {
                if (\is_string($value))
                {
                    $value = '\'' . self::pgsqlEscapeString($value) . '\'';
                }
            }
            $sql .= 'insert into ' . $tableStr . ' values(' . implode(',', $row) . ');' . \PHP_EOL;
        }

        return $sql;
    }

    /**
     * 转义 Pgsql 字符串.
     */
    public static function pgsqlEscapeString(string $value): string
    {
        return strtr($value, [
            "\0"     => '\0',
            "\n"     => '\n',
            "\r"     => '\r',
            "\t"     => '\t',
            \chr(26) => '\Z',
            \chr(8)  => '\b',
            '"'      => '\"',
            '\''     => '\\\'',
            '\\'     => '\\\\',
        ]);
    }
}
