<?php
namespace Imi\Test\Component\Tests;

use Imi\Util\Imi;
use Imi\Util\File;
use PHPUnit\Framework\Assert;

/**
 * @testdox Cache RedisHash Handler
 */
class CacheRedisHashTest extends BaseCacheTest
{
    protected $cacheName = 'redisHash';

    protected $supportTTL = false;

}
