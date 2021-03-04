<?php

declare(strict_types=1);

namespace Imi\Cache\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cache\Annotation\Cacheable;
use Imi\Cache\CacheManager;
use Imi\Config;
use Imi\Lock\Aop\TLockableParser;
use Imi\Util\ClassObject;

/**
 * @Aspect(priority=1024)
 */
class CacheableAop
{
    use TLockableParser;
    use TCacheAopHelper;

    /**
     * 处理 Cacheable 注解.
     *
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Cache\Annotation\Cacheable::class,
     *         }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parseCacheable(AroundJoinPoint $joinPoint)
    {
        $target = $joinPoint->getTarget();
        $class = get_parent_class($target);
        $method = $joinPoint->getMethod();
        $joinPointArgs = $joinPoint->getArgs();

        // Cacheable 注解
        $cacheable = AnnotationManager::getMethodAnnotations($class, $method, Cacheable::class)[0] ?? null;

        // 方法参数
        $args = ClassObject::convertArgsToKV($class, $method, $joinPointArgs);

        // 缓存名
        $name = $cacheable->name;
        if (null === $name)
        {
            $name = Config::get('@currentServer.cache.default');
            if (null === $name)
            {
                throw new \RuntimeException('Config "@currentServer.cache.default" not found');
            }
        }

        // 键
        $key = $this->getKey($joinPoint, $args, $cacheable);
        $cacheInstance = CacheManager::getInstance($name);

        // 尝试获取缓存值
        $cacheValue = $cacheInstance->get($key);
        if (null === $cacheValue)
        {
            if (null === $cacheable->lockable)
            {
                // 不加锁
                $nextProceedExeced = true;
                $cacheValue = $joinPoint->proceed();
            }
            else
            {
                // 加锁
                $nextProceedExeced = false;
                $this->parseLockable($target, $method, $joinPointArgs, $cacheable->lockable, function () use (&$cacheValue, $joinPoint, &$nextProceedExeced) {
                    $nextProceedExeced = true;
                    $cacheValue = $joinPoint->proceed();
                }, function () use ($cacheInstance, $key, &$cacheValue) {
                    return $cacheValue = $cacheInstance->get($key);
                });
            }
            if ($nextProceedExeced)
            {
                $cacheInstance->set($key, $cacheValue, $cacheable->ttl);
            }
        }

        return $cacheValue;
    }
}
