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
    public function testFilterableList(): void
    {
        $originData = [
            ['id' => 1, 'name' => 'a'],
        ];

        // 剔除 name 字段
        $list = new FilterableList($originData, ['name'], 'deny');
        $list->append($originData[] = ['id' => 2, 'name' => 'b']);
        foreach ($list as $k => $v)
        {
            $this->assertEquals([
                'id'  => $originData[$k]['id'],
            ], $v);
        }

        $this->assertEquals(json_encode([['id' => 1], ['id' => 2]]), json_encode($list));

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
        $this->assertNull($list[100]);

        $this->assertEquals([
            ['name'  =>  $originData[0]['name']],
            ['name'  => $originData[1]['name']],
        ], $list->toArray());

        $this->assertEquals(2, $list->count());
        $list[] = ['id' => 3, 'name' => 'c'];
        $this->assertEquals(3, $list->count());
        $list['test'] = ['id' => 4, 'name' => 'd'];
        $this->assertEquals(4, $list->count());

        $this->assertEquals(4, $list->count());
        $list->remove($list[0]);
        $this->assertEquals(3, $list->count());
        $this->assertTrue(isset($list['test']));
        unset($list['test']);
        $this->assertFalse(isset($list['test']));
        $this->assertEquals(2, $list->count());

        $list->clear();

        $this->assertEquals(0, $list->count());
    }

    public function testFilterableList2(): void
    {
        $object = new \stdClass();
        $object->id = 1;

        $list = new FilterableList([
            $object,
            ['id' => 2],
        ], null);
        $this->assertEquals(json_encode([
            ['id' => 1],
            ['id' => 2],
        ]), json_encode($list));

        $list = new FilterableList([
            $object,
            ['id' => 2],
        ], [], 'deny');
        $this->assertEquals(json_encode([
            ['id' => 1],
            ['id' => 2],
        ]), json_encode($list));
    }
}
