<?php

namespace Imi\Test\Component\Cache\Classes;

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
     * @param int $id
     *
     * @return void
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
     *
     * @param int $id
     *
     * @return void
     */
    public function testCacheableTTL(int $id)
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
     *
     * @param int $id
     *
     * @return void
     */
    public function testCacheableLock(int $id)
    {
        usleep(10000);

        return [
            'id'    => $id,
            'time'  => microtime(true),
        ];
    }

    /**
     * @CacheEvict(key="test:{id}")
     *
     * @return bool
     */
    public function testCacheEvict($id)
    {
        return true;
    }

    /**
     * @CachePut(key="test:{id}")
     *
     * @return void
     */
    public function testCachePut($id)
    {
        return $id;
    }
}
