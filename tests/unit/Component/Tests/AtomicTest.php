<?php

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Util\AtomicManager;
use PHPUnit\Framework\Assert;

/**
 * @testdox Atomic
 */
class AtomicTest extends BaseTest
{
    public function testGetAndSet()
    {
        Assert::assertEquals(0, AtomicManager::get('test'));
        AtomicManager::set('test', 100);
        Assert::assertEquals(100, AtomicManager::get('test'));
    }

    public function testAdd()
    {
        AtomicManager::set('test', 0);
        Assert::assertEquals(1, AtomicManager::add('test'));
        Assert::assertEquals(1, AtomicManager::get('test'));
        Assert::assertEquals(3, AtomicManager::add('test', 2));
        Assert::assertEquals(3, AtomicManager::get('test'));
    }

    public function testSub()
    {
        AtomicManager::set('test', 100);
        Assert::assertEquals(99, AtomicManager::sub('test'));
        Assert::assertEquals(99, AtomicManager::get('test'));
        Assert::assertEquals(97, AtomicManager::sub('test', 2));
        Assert::assertEquals(97, AtomicManager::get('test'));
    }

    public function testCmpset()
    {
        AtomicManager::set('test', 1);
        Assert::assertFalse(AtomicManager::cmpset('test', 0, 2));
        Assert::assertTrue(AtomicManager::cmpset('test', 1, 2));
        Assert::assertEquals(2, AtomicManager::get('test'));
    }
}
