<?php
namespace Imi\Cache\Aop;

use Imi\Config;
use Imi\Aop\PointCutType;
use Imi\Util\ClassObject;
use Imi\Cache\CacheManager;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Lock\Aop\TLockableParser;
use Imi\Cache\Annotation\CachePut;
use Imi\Cache\Annotation\Cacheable;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Aspect
 */
class CachePutAop
{
    use TCacheAopHelper;

    /**
     * 处理 CachePut 注解
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Cache\Annotation\CachePut::class,
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parseCachePut(AroundJoinPoint $joinPoint)
    {
        $methodReturn = $joinPoint->proceed();
        $method = $joinPoint->getMethod();

        $class = get_parent_class($joinPoint->getTarget());

        // CachePut 注解列表
        $cachePuts = AnnotationManager::getMethodAnnotations($class, $method, CachePut::class);

        // 方法参数
        $args = ClassObject::convertArgsToKV($class, $method, $joinPoint->getArgs());

        foreach($cachePuts as $cachePut)
        {
            // 缓存名
            $name = $cachePut->name;
            if(null === $name)
            {
                $name = Config::get('@currentServer.cache.default');
                if(null === $name)
                {
                    throw new \RuntimeException('config "cache.default" not found');
                }
            }

            // 键
            $key = $this->getKey($joinPoint, $args, $cachePut);
            $cacheInstance = CacheManager::getInstance($name);

            $cacheInstance->set($key, $this->getValue($cachePut, $methodReturn), $cachePut->ttl);
        }

        return $methodReturn;
    }
}