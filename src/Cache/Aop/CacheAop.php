<?php
namespace Imi\Cache\Aop;

use Imi\App;
use Imi\Config;
use Imi\Aop\JoinPoint;
use Imi\Aop\PointCutType;
use Imi\Bean\BeanFactory;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Util\ObjectArrayHelper;
use Imi\Aop\Annotation\PointCut;
use Imi\Lock\Annotation\Lockable;
use Imi\Lock\Exception\LockFailException;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Cache\CacheManager;
use Imi\Cache\Annotation\Cacheable;
use Imi\Lock\Aop\TLockableParser;

/**
 * @Aspect(priority=1024)
 */
class CacheAop
{
    use TLockableParser;

    /**
     * 处理 @Cacheable 注解
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Cache\Annotation\Cacheable::class,
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parseCacheable(AroundJoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());

        // Cacheable 注解列表
        $cacheable = AnnotationManager::getMethodAnnotations($class, $joinPoint->getMethod(), Cacheable::class)[0] ?? null;

        // 方法参数
        $args = $this->getArgs($joinPoint);

        // 缓存名
        $name = $cacheable->name;
        if(null === $name)
        {
            $name = Config::get('@currentServer.cache.default');
            if(null === $name)
            {
                throw new \RuntimeException('config "cache.default" not found');
            }
        }

        // 键
        $key = $this->getKey($joinPoint, $args, $cacheable);
        $cacheInstance = CacheManager::getInstance($name);

        // 尝试获取缓存值
        $cacheValue = $cacheInstance->get($key);
        if(null === $cacheValue)
        {
            if(null === $cacheable->lockable)
            {
                // 不加锁
                $nextProceedExeced = true;
                $cacheValue = $joinPoint->proceed();
            }
            else
            {
                // 加锁
                $nextProceedExeced = false;
                $this->parseLockable($joinPoint->getTarget(), $joinPoint->getMethod(), $joinPoint->getArgs(), $cacheable->lockable, function() use(&$cacheValue, $joinPoint, &$nextProceedExeced){
                    $nextProceedExeced = true;
                    $cacheValue = $joinPoint->proceed();
                }, function() use($cacheInstance, $key, &$cacheValue){
                    return $cacheValue = $cacheInstance->get($key);
                });
            }
            if($nextProceedExeced)
            {
                $cacheInstance->set($key, $cacheValue, $cacheable->ttl);
            }
        }

        return $cacheValue;
    }

    /**
     * 获取方法参数数组，key=>value
     *
     * @param AroundJoinPoint $joinPoint
     * @return array
     */
    private function getArgs(AroundJoinPoint $joinPoint)
    {
        $className = BeanFactory::getObjectClass($joinPoint->getTarget());
        $method = $joinPoint->getMethod();
        $_args = $joinPoint->getArgs();
        $methodRef = new \ReflectionMethod($className, $method);
        $args = [];
        foreach($methodRef->getParameters() as $i => $param)
        {
            $args[$param->name] = $_args[$i];
        }
        return $args;
    }

    /**
     * 获取缓存key
     *
     * @param \Imi\Aop\AroundJoinPoint $joinPoint
     * @param array $args
     * @param \Imi\Cache\Annotation\Cacheable $cacheable
     * @return string
     */
    private function getKey(AroundJoinPoint $joinPoint, $args, Cacheable $cacheable)
    {
        if(null === $cacheable->key)
        {
            return md5(
                get_parent_class($joinPoint->getTarget())
                . '::'
                . $joinPoint->getMethod()
                . '('
                . serialize($args)
                . ')'
            );
        }
        else
        {
            return preg_replace_callback('/\{([^\}]+)\}/', function($matches) use($args){
                return ObjectArrayHelper::get($args, $matches[1]);
            }, $cacheable->key);
        }
    }

    /**
     * 获取缓存值
     *
     * @param Cacheable $cacheable
     * @param mixed $value
     * @return mixed
     */
    private function getValue(Cacheable $cacheable, $value)
    {
        if(null === $cacheable->value)
        {
            return $value;
        }
        return ObjectArrayHelper::get($value, $cacheable->value);
    }

}