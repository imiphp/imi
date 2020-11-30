<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\ObjectArrayHelper;

/**
 * @testdox Imi\Util\ObjectArrayHelper
 */
class ObjectArrayHelperTest extends BaseTest
{
    private function getTestData()
    {
        $data2 = new \stdClass();
        $data2->id = 2;
        $data2->name = 'b';
        $data = [
            'name'          => 'imi',
            'url'           => 'https://www.imiphp.com',
            'description'   => 'imi is very six',
            'data1'         => [
                'id'    => 1,
                'name'  => 'a',
            ],
            'data2'         => $data2,
        ];

        return $data;
    }

    public function testGetAndSet()
    {
        $data = $this->getTestData();
        $this->assertEquals($data['data1']['name'], ObjectArrayHelper::get($data, 'data1.name'));

        ObjectArrayHelper::set($data, 'data2.name', $data['data2']->name . '-2');
        $this->assertEquals('b-2', $data['data2']->name);
        $this->assertEquals($data['data2']->name, ObjectArrayHelper::get($data, 'data2.name'));

        ObjectArrayHelper::set($data, 'description', 'imi niubi');
        $this->assertEquals('imi niubi', $data['description']);
        $this->assertEquals($data['description'], ObjectArrayHelper::get($data, 'description'));
    }

    public function testExists()
    {
        $data = $this->getTestData();
        $this->assertTrue(ObjectArrayHelper::exists($data, 'name'));
        $this->assertTrue(ObjectArrayHelper::exists($data, 'data1.name'));
        $this->assertTrue(ObjectArrayHelper::exists($data, 'data2.name'));
        $this->assertFalse(ObjectArrayHelper::exists($data, 'name1'));
        $this->assertFalse(ObjectArrayHelper::exists($data, 'data1.name1'));
        $this->assertFalse(ObjectArrayHelper::exists($data, 'data2.name1'));
    }

    public function testRemove()
    {
        $data = $this->getTestData();
        $this->assertTrue(ObjectArrayHelper::exists($data, 'name'));
        $this->assertTrue(ObjectArrayHelper::exists($data, 'data1.name'));
        $this->assertTrue(ObjectArrayHelper::exists($data, 'data2.name'));

        ObjectArrayHelper::remove($data, 'name');
        $this->assertFalse(ObjectArrayHelper::exists($data, 'name'));
        ObjectArrayHelper::remove($data, 'data1.name');
        $this->assertFalse(ObjectArrayHelper::exists($data, 'data1.name'));
        ObjectArrayHelper::remove($data, 'data2.name');
        $this->assertFalse(ObjectArrayHelper::exists($data, 'data2.name'));
    }

    public function testColumn()
    {
        $list = [
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
            ['id' => 3, 'name' => 'c'],
        ];
        $this->assertEquals(['a', 'b', 'c'], ObjectArrayHelper::column($list, 'name'));

        $list = [];
        $list[] = $item = new \stdClass();
        $item->id = 1;
        $item->name = 'a';
        $list[] = $item = new \stdClass();
        $item->id = 2;
        $item->name = 'b';
        $list[] = $item = new \stdClass();
        $item->id = 3;
        $item->name = 'c';
        $this->assertEquals(['a', 'b', 'c'], ObjectArrayHelper::column($list, 'name'));
    }

    public function testFilter()
    {
        // array
        $data = [
            'id'      => 1,
            'name'    => 'imi',
        ];
        // 只保留 name 字段
        ObjectArrayHelper::filter($data, ['name']);
        $this->assertEquals([
            'name'    => 'imi',
        ], $data);

        $data = [
            'id'      => 1,
            'name'    => 'imi',
        ];
        // 剔除 name 字段
        ObjectArrayHelper::filter($data, ['name'], 'deny');
        $this->assertEquals([
            'id'    => 1,
        ], $data);

        // object
        $data = new \stdClass();
        $data->id = 1;
        $data->name = 'imi';
        // 只保留 name 字段
        ObjectArrayHelper::filter($data, ['name']);
        $this->assertEquals('imi', $data->name);
        $this->assertFalse(ObjectArrayHelper::exists($data, 'id'));

        $data = new \stdClass();
        $data->id = 1;
        $data->name = 'imi';
        // 剔除 name 字段
        ObjectArrayHelper::filter($data, ['name'], 'deny');
        $this->assertEquals(1, $data->id);
        $this->assertFalse(ObjectArrayHelper::exists($data, 'name'));
    }
}
