<?php
namespace Imi\Cache\Aop;

use Imi\Config;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Cache\CacheManager;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\Before;
use Imi\Aop\Annotation\PointCut;
use Imi\Cache\Annotation\CacheEvict;
use Imi\Bean\Annotation\AnnotationManager;

/**
 * @Aspect
 */
class CacheEvictAop
{
    use TCacheAopHelper;

    /**
     * 处理 CacheEvict 注解
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Cache\Annotation\CacheEvict::class,
     *         }
     * )
     * @Before
     * 
     * @param JoinPoint $joinPoint
     * @return void
     */
    public function parseCacheEvict(JoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());

        // CacheEvict 注解列表
        $cacheEvicts = AnnotationManager::getMethodAnnotations($class, $joinPoint->getMethod(), CacheEvict::class);

        foreach($cacheEvicts as $cacheEvict)
        {
            // 方法参数
            $args = $this->getArgs($joinPoint);

            // 缓存名
            $name = $cacheEvict->name;
            if(null === $name)
            {
                $name = Config::get('@currentServer.cache.default');
                if(null === $name)
                {
                    throw new \RuntimeException('config "cache.default" not found');
                }
            }

            // 键
            $key = $this->getKey($joinPoint, $args, $cacheEvict);
            $cacheInstance = CacheManager::getInstance($name);

            $cacheInstance->delete($key);
        }

    }

}