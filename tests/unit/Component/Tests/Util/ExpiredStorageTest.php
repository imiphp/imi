<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\ExpiredStorage;

class ExpiredStorageTest extends BaseTest
{
    public function testExpiredStorage(): void
    {
        $storage = new ExpiredStorage([
            'a' => 123,
        ]);
        $this->assertTrue($storage->isset('a'));
        $this->assertEquals(123, $storage->get('a', null, $item));
        $this->assertGreaterThan(0, $item->getLastModifyTime());
        $this->assertEquals(0, $item->getTTL());
        $this->assertEquals(123, $item->getValue());

        $storage->set('a', 456, 0.1);
        $this->assertEquals(456, $storage->get('a', null, $item));
        $this->assertGreaterThan(0, $item->getLastModifyTime());
        $this->assertFalse($item->isExpired());
        $this->assertEquals(0.1, $item->getTTL());
        $this->assertEquals(456, $item->getValue());
        usleep(110_000);
        $this->assertEquals('default', $storage->get('a', 'default', $item));
        $this->assertGreaterThan(0, $item->getLastModifyTime());
        $this->assertTrue($item->isExpired());
        $this->assertEquals(0.1, $item->getTTL());
        $this->assertEquals(456, $item->getValue());

        $this->assertFalse($storage->isset('b'));
        $this->assertEquals('abc', $storage->get('b', 'abc'));
        $storage->set('b', 'def');
        $this->assertEquals('def', $storage->get('b'));
        $storage->unset('b');
        $this->assertNull($storage->get('b'));
        $this->assertFalse($storage->isset('b'));

        $items = $storage->getItems();
        $this->assertCount(1, $items);

        $storage->clear();

        $items = $storage->getItems();
        $this->assertCount(0, $items);
    }
}
