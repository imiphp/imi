<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Test\Component\Util\ArrayList\TestArrayListItem;
use Imi\Util\ArrayList;

/**
 * @testdox Imi\Util\ArrayList
 */
class ArrayListTest extends BaseTest
{
    public function testArrayListConstruct()
    {
        try
        {
            new ArrayList(TestArrayListItem::class, [
                1,
            ]);
            $this->assertTrue(false, 'ArrayList __construct set list not check type');
        }
        catch (\Throwable $th)
        {
            $this->assertTrue(true);
        }
    }

    public function testArrayList()
    {
        $list = [];
        for ($i = 1; $i <= 3; ++$i)
        {
            $list[] = new TestArrayListItem($i, 'imi-' . $i);
        }
        $arrayList = new ArrayList(TestArrayListItem::class, $list);

        // foreach
        foreach ($arrayList as $k => $v)
        {
            $this->assertEquals($list[$k], $v);
        }

        // count
        $this->assertEquals(3, $arrayList->count());
        $this->assertCount(3, $arrayList);

        // toArray
        $this->assertEquals($list, $arrayList->toArray());

        // json
        $this->assertEquals(json_encode($list), json_encode($arrayList));

        // get
        $item = $arrayList[1] ?? null;
        $this->assertEquals($list[1], $item);

        // set
        try
        {
            $arrayList[1] = null;
            $this->assertTrue(false, 'ArrayList set item not check type');
        }
        catch (\Throwable $th)
        {
            $this->assertTrue(true);
        }
        $arrayList[1] = new TestArrayListItem(100, 'imi-100');
        $this->assertEquals(100, $arrayList[1]->id);
        $this->assertEquals('imi-100', $arrayList[1]->name);
        $item = new TestArrayListItem(4, 'imi-4');
        $arrayList->append($item);
        $this->assertEquals($item, $arrayList[3] ?? null);

        // remove
        $this->assertEquals(4, $arrayList->count());
        unset($arrayList[1]);
        $this->assertEquals(3, $arrayList->count());
        $arrayList->remove($arrayList[0], $arrayList[2]);
        $this->assertEquals(1, $arrayList->count());

        // clear
        $arrayList->clear();
        $this->assertEquals(0, $arrayList->count());
    }
}
