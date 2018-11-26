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
        $lockable = AnnotationManager::getMethodAnnotations(get_parent_class($joinPoint->getTarget()), $joinPoint->getMethod(), Lockable::class)[0] ?? null;
        if(null === $lockable->type)
        {
            $type = Config::get('@currentServer.lock.defaultType', 'RedisLock');
        }
        else
        {
            $type = $lockable->type;
        }

        $options = $lockable->options;
        if(!isset($options['waitTimeout']))
        {
            $options['waitTimeout'] = $lockable->waitTimeout;
        }
        if(!isset($options['lockExpire']))
        {
            $options['lockExpire'] = $lockable->lockExpire;
        }

        $locker = App::getBean($type, $this->getId($joinPoint, $lockable), $options);

        if(!$locker->lock(function() use($joinPoint){
            $joinPoint->proceed();
        }))
        {
            throw new LockFailException(sprintf('get lock failed, id:%s', $locker->getId()));
        }
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