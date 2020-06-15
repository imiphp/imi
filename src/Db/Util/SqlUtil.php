<?php
namespace Imi\Db\Util;

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

}
