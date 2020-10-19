<?php

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Test\Component\Enum\TestEnum;
use PHPUnit\Framework\Assert;

/**
 * @testdox Enum
 */
class EnumTest extends BaseTest
{
    public function test()
    {
        $data = TestEnum::getData(TestEnum::A);
        Assert::assertEquals('甲', $data->text);
        Assert::assertEquals('a1', $data->other);

        Assert::assertEquals('B', TestEnum::getName(TestEnum::B));
        Assert::assertEquals([
            'A',
            'B',
            'C',
        ], TestEnum::getNames());
        Assert::assertEquals([
            TestEnum::A,
            TestEnum::B,
            TestEnum::C,
        ], TestEnum::getValues());

        Assert::assertEquals('丙', TestEnum::getText(TestEnum::C));
        Assert::assertEquals(TestEnum::B, TestEnum::getValue('B'));
        Assert::assertEquals([
            'A' => TestEnum::A,
            'B' => TestEnum::B,
            'C' => TestEnum::C,
        ], TestEnum::getMap());
    }
}
