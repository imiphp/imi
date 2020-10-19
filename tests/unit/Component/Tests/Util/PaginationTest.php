<?php

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Pagination;

/**
 * @testdox Imi\Util\Pagination
 */
class PaginationTest extends BaseTest
{
    public function testPagination()
    {
        $pagination = new Pagination(1, 11);
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(11, $pagination->getCount());
        $this->assertEquals(0, $pagination->getLimitOffset());
        $this->assertEquals(10, $pagination->getLimitEndOffset());
        $this->assertEquals(1, $pagination->calcPageCount(1));
        $this->assertEquals(1, $pagination->calcPageCount(11));
        $this->assertEquals(9, $pagination->calcPageCount(99));
        $this->assertEquals(10, $pagination->calcPageCount(100));

        $pagination->setPage(5);
        $this->assertEquals(5, $pagination->getPage());
        $pagination->setCount(10);
        $this->assertEquals(10, $pagination->getCount());
        $this->assertEquals(40, $pagination->getLimitOffset());
        $this->assertEquals(49, $pagination->getLimitEndOffset());
        $this->assertEquals(1, $pagination->calcPageCount(1));
        $this->assertEquals(1, $pagination->calcPageCount(10));
        $this->assertEquals(2, $pagination->calcPageCount(11));
        $this->assertEquals(11, $pagination->calcPageCount(101));
    }
}
