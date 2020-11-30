<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\LazyArrayObject;

/**
 * @testdox Imi\Util\LazyArrayObject
 */
class LazyArrayObjectTest extends BaseTest
{
    public function testLazyArrayObject()
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
        $data = new LazyArrayObject($rawData);

        $this->assertEquals($rawData, $data->toArray());
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
        $this->assertEquals('a', $data->data1['name']);

        $this->assertEquals(2, $data->data2->id);
        $this->assertEquals('b', $data->data2->name);

        $data['description'] .= '-1';
        $this->assertEquals('imi is very six-1', $data->description);
        $data->description .= '-2';
        $this->assertEquals('imi is very six-1-2', $data['description']);

        $this->assertFalse(isset($data->a));
        $this->assertFalse(isset($data['a']));
        $data->a = ['b' => 1];
        $this->assertTrue(isset($data->a));
        $this->assertTrue(isset($data['a']));
        $this->assertEquals(1, $data->a['b']);

        unset($data['a']);
        $this->assertNull($data->a);
        $this->assertNull($data['a']);
        $this->assertFalse(isset($data->a));
        $this->assertFalse(isset($data['a']));

        unset($data->x);
        $this->assertNull($data->x);
        $this->assertNull($data['x']);
        $this->assertFalse(isset($data->x));
        $this->assertFalse(isset($data['x']));
    }
}
