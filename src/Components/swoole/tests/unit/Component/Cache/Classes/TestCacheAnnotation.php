<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Cache\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Cache\Annotation\Cacheable;
use Imi\Cache\Annotation\CacheEvict;
use Imi\Cache\Annotation\CachePut;
use Imi\Lock\Annotation\Lockable;

/**
 * @Bean("TestCacheAnnotation")
 */
class TestCacheAnnotation
{
    /**
     * @Cacheable(
     *   key="test:{id}",
     * )
     *
     * @return array|int
     */
    public function testCacheable(int $id)
    {
        return [
            'id'    => $id,
            'time'  => microtime(true),
        ];
    }

    /**
     * @Cacheable(
     *   key="test:{id}",
     *   ttl=1
     * )
     */
    public function testCacheableTTL(int $id): array
    {
        return [
            'id'    => $id,
            'time'  => microtime(true),
        ];
    }

    /**
     * @Cacheable(
     *   key="test:{id}",
     *   ttl=1,
     *   lockable=@Lockable(
     *     id="testCacheableLock:{id}",
     *     waitTimeout=999999,
     *   ),
     *   preventBreakdown=true,
     * )
     */
    public function testCacheableLock(int $id): array
    {
        usleep(10000);

        return [
            'id'    => $id,
            'time'  => microtime(true),
        ];
    }

    /**
     * @CacheEvict(key="test:{id}")
     */
    public function testCacheEvict(int $id): bool
    {
        return true;
    }

    /**
     * @CachePut(key="test:{id}")
     */
    public function testCachePut(int $id): int
    {
        return $id;
    }
}
