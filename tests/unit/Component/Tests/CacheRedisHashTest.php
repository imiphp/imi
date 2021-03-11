<?php

namespace Imi\Test\Component\Tests;

/**
 * @testdox Cache RedisHash Handler
 */
class CacheRedisHashTest extends BaseCacheTest
{
    /**
     * @var string
     */
    protected $cacheName = 'redisHash';

    /**
     * @var bool
     */
    protected $supportTTL = false;
}
