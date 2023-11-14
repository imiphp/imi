<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Test\Component\Enum\TestEnumBean;
use Imi\Test\Component\Enum\TestEnumBeanBacked;
use Imi\Util\EnumUtil;

class EnumTest extends BaseTest
{
    public function testFromName(): void
    {
        $this->assertEquals(TestEnumBean::A, EnumUtil::fromName(TestEnumBean::class, 'A'));
        $this->expectException(\ValueError::class);
        EnumUtil::fromName(TestEnumBean::class, 'ABC');
    }

    public function testTryFromName(): void
    {
        $this->assertEquals(TestEnumBean::A, EnumUtil::tryFromName(TestEnumBean::class, 'A'));
        $this->assertNull(EnumUtil::tryFromName(TestEnumBean::class, 'ABC'));
    }

    public function testIn(): void
    {
        $this->assertTrue(EnumUtil::in(TestEnumBean::class, 'A'));
        $this->assertTrue(EnumUtil::in(TestEnumBean::class, TestEnumBean::A));
        $this->assertFalse(EnumUtil::in(TestEnumBean::class, 'ABC'));

        $this->assertTrue(EnumUtil::in(TestEnumBeanBacked::class, 'hello'));
        $this->assertTrue(EnumUtil::in(TestEnumBeanBacked::class, TestEnumBeanBacked::A));
        $this->assertFalse(EnumUtil::in(TestEnumBeanBacked::class, 'hello imi'));
    }
}
