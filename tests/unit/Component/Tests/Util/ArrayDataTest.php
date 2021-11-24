<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\ArrayData;

/**
 * @testdox Imi\Util\ArrayData
 */
class ArrayDataTest extends BaseTest
{
    public function testArrayData(): void
    {
        $data2 = new \stdClass();
        $data2->id = 2;
        $data2->name = 'b';
        $rawData = [
            'name'          => 'imi',
            'url'           => 'https://www.imiphp.com',
            'description'   => 'imi is very six',
            'data1'         => [
                'id'    => 1,
                'name'  => 'a',
            ],
            'data2'         => $data2,
        ];
        $data = new ArrayData($rawData);

        $this->assertEquals($rawData, $data->getRawData());
        $this->assertEquals(5, $data->length());
        $this->assertCount(5, $data);
        foreach ($data as $k => $v)
        {
            $this->assertEquals($rawData[$k], $v);
        }

        $this->assertEquals('imi', $data->name);
        $this->assertEquals($data->name, $data['name']);
        $this->assertEquals('https://www.imiphp.com', $data->url);
        $this->assertEquals($data->url, $data['url']);
        $this->assertEquals('imi is very six', $data->description);
        $this->assertEquals($data->description, $data['description']);

        $this->assertEquals(1, $data->data1['id']);
        $this->assertEquals($data->data1['id'], $data['data1.id']);
        $this->assertEquals('a', $data->data1['name']);
        $this->assertEquals($data->data1['name'], $data['data1.name']);

        $this->assertEquals(2, $data->data2->id);
        $this->assertEquals($data->data2->id, $data['data2.id']);
        $this->assertEquals('b', $data->data2->name);
        $this->assertEquals($data->data2->name, $data['data2.name']);

        $data['description'] .= '-1';
        $this->assertEquals('imi is very six-1', $data->description);
        $data->description .= '-2';
        $this->assertEquals('imi is very six-1-2', $data['description']);
        $data->set('description', $data->description . '-3');
        $this->assertEquals('imi is very six-1-2-3', $data->get('description'));

        $this->assertFalse(isset($data->a));
        $this->assertFalse(isset($data['a']));
        $this->assertFalse($data->exists('a'));
        $data->set('a', [
            'b' => 1,
        ]);
        $this->assertTrue(isset($data->a));
        $this->assertTrue(isset($data['a']));
        $this->assertTrue($data->exists('a'));
        $this->assertEquals(1, $data->a['b']);
        $this->assertEquals($data->a['b'], $data['a.b']);
        $this->assertEquals($data->a['b'], $data->get('a.b'));
        $this->assertEquals([
            'b' => 1,
        ], $data->get('a'));

        $data->set([
            'a' => [
                'c' => 2,
            ],
        ], null, false);
        $this->assertTrue(isset($data->a));
        $this->assertTrue(isset($data['a']));
        $this->assertTrue($data->exists('a'));
        $this->assertFalse(isset($data->a->b));
        $this->assertFalse(isset($data['a.b']));
        $this->assertFalse($data->exists('a.b'));
        $this->assertEquals(2, $data->a['c']);
        $this->assertEquals($data->a['c'], $data['a.c']);
        $this->assertEquals($data->a['c'], $data->get('a.c'));
        $this->assertEquals([
            'c' => 2,
        ], $data->get('a'));

        $data->setVal('x.y', 1);
        $this->assertEquals(1, $data->x['y']);
        $this->assertEquals($data->x['y'], $data['x.y']);
        $this->assertEquals($data->x['y'], $data->get('x.y'));
        $this->assertEquals([
            'y' => 1,
        ], $data->get('x'));

        unset($data['a']);
        $this->assertEquals(404, $data->get('a', 404));
        $this->assertFalse($data->a);
        $this->assertFalse($data['a']);
        $this->assertFalse(isset($data->a));
        $this->assertFalse(isset($data['a']));
        $this->assertFalse($data->exists('a'));

        unset($data->x);
        $this->assertEquals(404, $data->get('x', 404));
        $this->assertFalse($data->x);
        $this->assertFalse($data['x']);
        $this->assertFalse(isset($data->x));
        $this->assertFalse(isset($data['x']));
        $this->assertFalse($data->exists('x'));

        $data->clear();
        $this->assertEmpty($data->getRawData());
    }
}
