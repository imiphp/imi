<?php
namespace Imi\Db\Traits;

use Imi\Util\Random;

trait SqlParser
{
    /**
     * 处理 :xxx 参数转为 ? 问号参数
     * @param string $sql
     * @param array $params
     * @return string
     */
    public function parseSqlNameParamsToQuestionMark($sql, &$params)
    {
        $params = [];
        $hash = Random::letter(16, 16);
        $items = [];
        $index = 0;
        // 先替换字符串
        $sql = preg_replace_callback('/\'([^\']+)\'/', function($matches) use($hash, &$items, &$index){
            $items[] = $matches[0];
            $returnValue = $hash . '-' . $index;
            ++$index;
            return $returnValue;
        }, $sql);

        // 匹配 :xxx 参数
        $i = 1;
        $sql = preg_replace_callback('/(\?|:\w+)/', function($matches) use(&$params, &$i){
            if('?' === $matches[1])
            {
                $params[] = $i;
            }
            else
            {
                $params[] = $matches[1];
            }
            ++$i;
            return '?';
        }, $sql);

        // 把开始的替换回来
        for($index -= 1; $index >= 0; --$index)
        {
            $sql = str_replace($hash .'-' . $index, $items[$index], $sql);
        }

        return $sql;
    }
}