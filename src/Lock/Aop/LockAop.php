<?php
namespace Imi\Lock\Aop;

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

/**
 * @Aspect
 */
class LockAop
{
    /**
     * 处理方法加锁
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             \Imi\Lock\Annotation\Lockable::class,
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parseLock(AroundJoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());

        // Lockable 注解
        $lockable = AnnotationManager::getMethodAnnotations($class, $joinPoint->getMethod(), Lockable::class)[0] ?? null;

        // Lock 类型
        if(null === $lockable->type)
        {
            $type = Config::get('@currentServer.lock.defaultType', 'RedisLock');
        }
        else
        {
            $type = $lockable->type;
        }

        // options
        $options = $lockable->options;
        if(!isset($options['waitTimeout']))
        {
            $options['waitTimeout'] = $lockable->waitTimeout;
        }
        if(!isset($options['lockExpire']))
        {
            $options['lockExpire'] = $lockable->lockExpire;
        }

        // Lock 对象
        $locker = App::getBean($type, $this->getId($joinPoint, $lockable), $options);

        // afterLock 处理
        $afterLock = $lockable->afterLock;
        if(is_array($afterLock) && isset($afterLock[0]) && '$this' === $afterLock[0])
        {
            // 用反射实现调用 protected 方法
            $refMethod = new \ReflectionMethod($class . '::' . $afterLock[1]);
            $afterLock = $refMethod->getClosure($joinPoint->getTarget());
        }
        $afterLockCallable = function() use($afterLock, &$result){
            $result = $afterLock();
            return null !== $result;
        };

        if(!$locker->lock(function() use($joinPoint, &$result){
            // 执行原方法
            $result = $joinPoint->proceed();
        }, $afterLockCallable))
        {
            throw new LockFailException(sprintf('get lock failed, id:%s', $locker->getId()));
        }

        return $result;
    }

    /**
     * 获取ID
     *
     * @param AroundJoinPoint $joinPoint
     * @param \Imi\Lock\Annotation\Lockable $lockable
     * @return string
     */
    private function getId(AroundJoinPoint $joinPoint, Lockable $lockable)
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
        if(null === $lockable->id)
        {
            return md5(
                $className
                . '::'
                . $method
                . '('
                . serialize($args)
                . ')'
            );
        }
        else
        {
            return preg_replace_callback('/\{([^\}]+)\}/', function($matches) use($args){
                return ObjectArrayHelper::get($args, $matches[1]);
            }, $lockable->id);
        }
    }

}