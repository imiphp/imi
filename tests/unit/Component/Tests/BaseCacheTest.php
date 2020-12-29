<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Cache\CacheManager;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

abstract class BaseCacheTest extends BaseTest
{
    protected $cacheName = null;

    protected $supportTTL = true;

    public function testSetAndGet()
    {
        Assert::assertTrue(CacheManager::set($this->cacheName, 'imi', 'nb'));
        Assert::assertEquals('nb', CacheManager::get($this->cacheName, 'imi'));
    }

    /**
     * @testdox Set TTL
     *
     * @return void
     */
    public function testSetTTL()
    {
        if (!$this->supportTTL)
        {
            $this->markTestSkipped('Handler does not support TTL');
        }
        Assert::assertTrue(CacheManager::set($this->cacheName, 'imi', 'nb', 2));
        Assert::assertEquals('nb', CacheManager::get($this->cacheName, 'imi'));
        usleep(2100000);
        Assert::assertEquals('none', CacheManager::get($this->cacheName, 'imi', 'none'));
    }

    public function testSetMultiple()
    {
        $values = [
            'k1'    => 'v1',
            'k2'    => 'v2',
        ];
        Assert::assertTrue(CacheManager::setMultiple($this->cacheName, $values));
        $getValues = CacheManager::getMultiple($this->cacheName, array_keys($values));
        Assert::assertEquals($values, $getValues);
    }

    /**
     * @testdox Set multiple TTL
     *
     * @return void
     */
    public function testSetMultipleTTL()
    {
        if (!$this->supportTTL)
        {
            $this->markTestSkipped('Handler does not support TTL');
        }
        for ($_ = 0; $_ < 3; ++$_)
        {
            try
            {
                $th = null;
                $values = [
                    'k1'    => 'v1',
                    'k2'    => 'v2',
                ];
                Assert::assertTrue(CacheManager::setMultiple($this->cacheName, $values, 1));
                $getValues = CacheManager::getMultiple($this->cacheName, array_keys($values));
                Assert::assertEquals($values, $getValues);
                usleep(1100000);
                Assert::assertEquals([
                    'k1'    => 'none',
                    'k2'    => 'none',
                ], CacheManager::getMultiple($this->cacheName, array_keys($values), 'none'));
                break;
            }
            catch (\Throwable $th)
            {
                sleep(1);
            }
        }
        if (isset($th))
        {
            throw $th;
        }
    }

    public function testDelete()
    {
        Assert::assertTrue(CacheManager::set($this->cacheName, 'imi', 'nb'));
        Assert::assertEquals('nb', CacheManager::get($this->cacheName, 'imi'));
        Assert::assertTrue(CacheManager::delete($this->cacheName, 'imi'));
        Assert::assertNull(CacheManager::get($this->cacheName, 'imi'));
    }

    public function testDeleteMultiple()
    {
        $values = [
            'k1'    => 'v1',
            'k2'    => 'v2',
        ];
        Assert::assertTrue(CacheManager::setMultiple($this->cacheName, $values));
        $getValues = CacheManager::getMultiple($this->cacheName, array_keys($values));
        Assert::assertEquals($values, $getValues);

        Assert::assertTrue(CacheManager::deleteMultiple($this->cacheName, array_keys($values)));
        Assert::assertEquals([
            'k1'    => null,
            'k2'    => null,
        ], CacheManager::getMultiple($this->cacheName, array_keys($values)));
    }

    public function testHas()
    {
        Assert::assertTrue(CacheManager::set($this->cacheName, 'imi', 'nb'));
        Assert::assertTrue(CacheManager::has($this->cacheName, 'imi'));
        Assert::assertTrue(CacheManager::delete($this->cacheName, 'imi'));
        Assert::assertFalse(CacheManager::has($this->cacheName, 'imi'));
    }

    public function testClear()
    {
        $values = [
            'k1'    => 'v1',
            'k2'    => 'v2',
        ];
        Assert::assertTrue(CacheManager::setMultiple($this->cacheName, $values));
        $getValues = CacheManager::getMultiple($this->cacheName, array_keys($values));
        Assert::assertEquals($values, $getValues);

        Assert::assertTrue(CacheManager::clear($this->cacheName));
        Assert::assertEquals([
            'k1'    => null,
            'k2'    => null,
        ], CacheManager::getMultiple($this->cacheName, array_keys($values)));
    }
}
