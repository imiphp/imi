<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

/**
 * @testdox Cache Redis Handler
 */
class CacheRedisTest extends BaseCacheTest
{
    protected string $cacheName = 'redis';
}
