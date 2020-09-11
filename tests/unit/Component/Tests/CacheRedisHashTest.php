<?php

namespace Imi\Test\Component\Tests;

/**
 * @testdox Cache RedisHash Handler
 */
class CacheRedisHashTest extends BaseCacheTest
{
    protected $cacheName = 'redisHash';

    protected $supportTTL = false;
}
