<?php

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
     *
     * @return void
     */
    public function testStartwith()
    {
        $this->assertTrue(Text::startwith('imi is very niu bi', 'imi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'niu bi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'Imi'));
        $this->assertFalse(Text::startwith('imi is very niu bi', 'Imi', true));
    }

    /**
     * @testdox endwith
     *
     * @return void
     */
    public function testEndwith()
    {
        $this->assertTrue(Text::endwith('imi is very niu bi', 'niu bi'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'imi'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'Niu BI'));
        $this->assertFalse(Text::endwith('imi is very niu bi', 'Niu BI', true));
    }

    /**
     * @testdox insert
     *
     * @return void
     */
    public function testInsert()
    {
        $string = 'imi is niu bi';
        $this->assertEquals('imi is very niu bi', Text::insert($string, 7, 'very '));
    }

    /**
     * @testdox isEmpty
     *
     * @return void
     */
    public function testIsEmpty()
    {
        $this->assertTrue(Text::isEmpty(''));
        $this->assertTrue(Text::isEmpty(null));
        $this->assertFalse(Text::isEmpty(0));
        $this->assertFalse(Text::isEmpty('1'));
    }

    /**
     * @testdox toCamelName
     *
     * @return void
     */
    public function testToCamelName()
    {
        $this->assertEquals('tbAdminMember', Text::toCamelName('tb_admin_member'));
    }

    /**
     * @testdox toPascalName
     *
     * @return void
     */
    public function testToPascalName()
    {
        $this->assertEquals('TbAdminMember', Text::toPascalName('tb_admin_member'));
    }

    /**
     * @testdox toUnderScoreCase
     *
     * @return void
     */
    public function testToUnderScoreCase()
    {
        $this->assertEquals('tb_admin_member', Text::toUnderScoreCase('tbAdminMember'));
        $this->assertEquals('tb_admin_member', Text::toUnderScoreCase('TbAdminMember'));
    }
}
