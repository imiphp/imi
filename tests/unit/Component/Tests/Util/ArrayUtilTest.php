<?php

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\ArrayUtil;

/**
 * @testdox Imi\Util\ArrayUtil
 */
class ArrayUtilTest extends BaseTest
{
    /**
     * @testdox remove
     */
    public function testRemove()
    {
        $list = [];
        for ($i = 0; $i < 10; ++$i)
        {
            $obj = new \stdClass();
            $obj->index = $i;
            $list[] = $obj;
        }
        $resultList = ArrayUtil::remove($list, $list[1], $list[3], $list[9]);
        $this->assertCount(7, $resultList);
        for ($i = 0; $i < 7; ++$i)
        {
            $this->assertTrue(isset($resultList[$i]));
        }
        $this->assertNotEquals($list[1], $resultList[1] ?? null);
        $this->assertNotEquals($list[3], $resultList[3] ?? null);
        $this->assertNotEquals($list[9], $resultList[9] ?? null);
    }

    /**
     * @testdox removeKeepKey
     */
    public function testRemoveKeepKey()
    {
        $list = [];
        for ($i = 0; $i < 10; ++$i)
        {
            $obj = new \stdClass();
            $obj->index = $i;
            $list[] = $obj;
        }
        $resultList = ArrayUtil::removeKeepKey($list, $list[1], $list[3], $list[9]);
        $this->assertCount(7, $resultList);
        $this->assertNull($resultList[1] ?? null);
        $this->assertNull($resultList[3] ?? null);
        $this->assertNull($resultList[9] ?? null);
    }

    /**
     * @testdox recursiveMerge
     */
    public function testRecursiveMerge()
    {
        $arr1 = [
            'a' => [
                'a-1'   => [
                    'id'    => 1,
                    'name'  => 'yurun',
                ],
                'a-2'   => [
                    'id'    => 2,
                    'name'  => 'imi',
                ],
                'a-3'   => [
                    'id'    => 3,
                    'name'  => 'https://www.yurunsoft.com',
                ],
            ],
            'c' => [
                100 => '1',
                200 => 'b',
            ],
        ];
        $arr2 = [
            'a' => [
                'a-1'   => [
                    'name'  => 'https://www.imiphp.com',
                ],
                'a-2'   => 100,
            ],
            'b' => 200,
            'c' => [
                100 => 'a',
                300 => 'c',
            ],
        ];
        $actual = [
            'a' => [
                'a-1'   => [
                    'id'    => 1,
                    'name'  => 'https://www.imiphp.com',
                ],
                'a-2'   => 100,
                'a-3'   => [
                    'id'    => 3,
                    'name'  => 'https://www.yurunsoft.com',
                ],
            ],
            'b' => 200,
            'c' => [
                100 => 'a',
                200 => 'b',
                300 => 'c',
            ],
        ];
        $result = ArrayUtil::recursiveMerge($arr1, $arr2);
        $this->assertEquals($actual, $result);
    }

    /**
     * @testdox columnToKey
     */
    public function testColumnToKey()
    {
        $list = [
            ['id' => 11, 'title' => 'aaa'],
            ['id' => 22, 'title' => 'bbb'],
            ['id' => 33, 'title' => 'ccc'],
        ];
        $actualKeepOld = [
            11 => ['id' => 11, 'title' => 'aaa'],
            22 => ['id' => 22, 'title' => 'bbb'],
            33 => ['id' => 33, 'title' => 'ccc'],
        ];
        $actualNotKeepOld = [
            11 => ['title' => 'aaa'],
            22 => ['title' => 'bbb'],
            33 => ['title' => 'ccc'],
        ];
        $this->assertEquals($actualKeepOld, ArrayUtil::columnToKey($list, 'id'));
        $this->assertEquals($actualNotKeepOld, ArrayUtil::columnToKey($list, 'id', false));
    }

    /**
     * @testdox isAssoc
     */
    public function testIsAssoc()
    {
        $assocArr = [
            0   => 'a',
            1   => 'b',
            2   => 'c',
            'a' => 'd',
        ];
        $indexArr = [
            'a', 'b', 'c',
        ];
        $this->assertTrue(ArrayUtil::isAssoc($assocArr));
        $this->assertFalse(ArrayUtil::isAssoc($indexArr));
    }

    /**
     * @testdox random
     */
    public function testRandom()
    {
        $arr = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ];
        $result = ArrayUtil::random($arr, 3);
        foreach ($result as $k => $v)
        {
            $this->assertEquals($arr[$k] ?? null, $v);
        }

        $result = ArrayUtil::random($arr, 1);
        foreach ($result as $k => $v)
        {
            $this->assertEquals($arr[$k] ?? null, $v);
        }
    }

    /**
     * @testdox toTreeAssoc
     */
    public function testToTreeAssoc()
    {
        $list = [
            ['id' => '1', 'parentId' => '0', 'name' => 'a'],
            ['id' => '2', 'parentId' => '0', 'name' => 'b'],
            ['id' => '3', 'parentId' => '0', 'name' => 'c'],
            ['id' => '4', 'parentId' => '1', 'name' => 'a-1'],
            ['id' => '5', 'parentId' => '1', 'name' => 'a-2'],
            ['id' => '6', 'parentId' => '4', 'name' => 'a-1-1'],
            ['id' => '7', 'parentId' => '4', 'name' => 'a-1-2'],
            ['id' => '8', 'parentId' => '2', 'name' => 'b-1'],
            ['id' => '9', 'parentId' => '2', 'name' => 'b-2'],
        ];
        $expected = [
            ['id' => '1', 'parentId' => '0', 'name' => 'a', 'children' => [
                ['id' => '4', 'parentId' => '1', 'name' => 'a-1', 'children' => [
                    ['id' => '6', 'parentId' => '4', 'name' => 'a-1-1', 'children' => []],
                    ['id' => '7', 'parentId' => '4', 'name' => 'a-1-2', 'children' => []],
                ]],
                ['id' => '5', 'parentId' => '1', 'name' => 'a-2', 'children' => []],
            ]],
            ['id' => '2', 'parentId' => '0', 'name' => 'b', 'children' => [
                ['id' => '8', 'parentId' => '2', 'name' => 'b-1', 'children' => []],
                ['id' => '9', 'parentId' => '2', 'name' => 'b-2', 'children' => []],
            ]],
            ['id' => '3', 'parentId' => '0', 'name' => 'c', 'children' => []],
        ];
        $this->assertEquals($expected, ArrayUtil::toTreeAssoc($list, 'id', 'parentId'));
    }
}
