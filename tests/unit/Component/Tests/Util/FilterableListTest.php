<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\FilterableList;

/**
 * @testdox Imi\Util\FilterableList
 */
class FilterableListTest extends BaseTest
{
    public function testFilterableList()
    {
        $originData = [
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
        ];

        // 剔除 name 字段
        $list = new FilterableList($originData, ['name'], 'deny');
        foreach ($list as $k => $v)
        {
            $this->assertEquals([
                'id'  => $originData[$k]['id'],
            ], $v);
        }

        // 只保留 name 字段
        $list = new FilterableList($originData, ['name']);
        foreach ($list as $k => $v)
        {
            $this->assertEquals([
                'name'  => $originData[$k]['name'],
            ], $v);
        }

        $this->assertEquals([
            'name'  => $originData[0]['name'],
        ], $list[0]);

        $this->assertEquals([
            ['name'  =>  $originData[0]['name']],
            ['name'  => $originData[1]['name']],
        ], $list->toArray());

        $this->assertEquals(2, $list->count());

        $list->remove($list[0]);

        $this->assertEquals(1, $list->count());

        $this->assertEquals([
            ['name'  =>  $originData[1]['name']],
        ], $list->toArray());

        $list->clear();

        $this->assertEquals(0, $list->count());
    }
}
