<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

/**
 * @testdox Cache RedisHash Handler
 */
class CacheRedisHashTest extends BaseCacheTest
{
    protected string $cacheName = 'redisHash';

    protected bool $supportTTL = false;
}
