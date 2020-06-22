<?php
namespace Imi\Db\Util;

use Imi\Db\Exception\DbException;

abstract class SqlUtil
{
    /**
     * 解析带冒号参数的 SQL，返回解析后的 SQL
     * @param string $sql
     * @param array $map
     * @return string
     */
    public static function parseSqlWithColonParams(string $sql, ?array &$map)
    {
        $map = [];
        return preg_replace_callback('/:[a-zA-Z0-9_]+/', function($match) use(&$map){
            $map[] = $match[0];
            return '?';
        }, $sql);
    }

    /**
     * 处理多行 SQL 为一个数组
     *
     * @param string $sql
     * @return array
     */
    public static function parseMultiSql(string $sql): array
    {
        $result = [];
        $begin = 0;
        $i = 0;
        $sqlLength = strlen($sql);
        // 关闭单引号
        $closeApostrophe = true;
        for($i = 0; $i < $sqlLength; ++$i)
        {
            switch($sql[$i])
            {
                case ';':
                    if($closeApostrophe)
                    {
                        $result[] = trim(substr($sql, $begin, $i + 1 - $begin));
                        $begin = $i + 1;
                    }
                    break;
                case '\\':
                    $next = $i + 1;
                    if(isset($sql[$next]) && '\'' === $sql[$next])
                    {
                        // 下个字符是单引号，算转义，跳过
                        $i = $next;
                    }
                    break;
                case '\'':
                    $next = $i + 1;
                    if(isset($sql[$next]) && '\'' === $sql[$next])
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
        if('' !== $leftSql)
        {
            throw new DbException(sprintf('Invalid sql: %s', trim($leftSql)));
        }
        return $result;
    }

}
