<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Text;

/**
 * @testdox Imi\Util\Text
 */
class TextTest extends BaseTest
{
    /**
     * @testdox startwith
     */
    public function testStartwith(): void
    {
        $this->assertTrue(Text::startwith('imi is very niu bi', 'imi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'niu bi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'Imi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'Imi', true));
    }

    /**
     * @testdox endwith
     */
    public function testEndwith(): void
    {
        $this->assertTrue(Text::endwith('imi is very niu bi', 'niu bi'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'imi'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'Niu BI'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'Niu BI', true));
    }

    /**
     * @testdox insert
     */
    public function testInsert(): void
    {
        $string = 'imi is niu bi';
        $this->assertEquals('imi is very niu bi', Text::insert($string, 7, 'very '));
    }

    /**
     * @testdox isEmpty
     */
    public function testIsEmpty(): void
    {
        $this->assertTrue(Text::isEmpty(''));
        $this->assertTrue(Text::isEmpty(null));
        $this->assertFalse(Text::isEmpty('0'));
        $this->assertFalse(Text::isEmpty('1'));
    }

    /**
     * @testdox toCamelName
     */
    public function testToCamelName(): void
    {
        $this->assertEquals('tbAdminMember', Text::toCamelName('tb_admin_member'));
    }

    /**
     * @testdox toPascalName
     */
    public function testToPascalName(): void
    {
        $this->assertEquals('TbAdminMember', Text::toPascalName('tb_admin_member'));
    }

    /**
     * @testdox toUnderScoreCase
     */
    public function testToUnderScoreCase(): void
    {
        $this->assertEquals('tb_admin_member', Text::toUnderScoreCase('tbAdminMember'));
        $this->assertEquals('tb_admin_member', Text::toUnderScoreCase('TbAdminMember'));
    }
}
