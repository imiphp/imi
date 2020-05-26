<?php
namespace Imi\Lock\Aop;

use Imi\App;
use Imi\Config;
use Imi\Util\ClassObject;
use Imi\Util\ObjectArrayHelper;
use Imi\Lock\Annotation\Lockable;
use Imi\Bean\ReflectionContainer;
use Imi\Lock\Exception\LockFailException;

trait TLockableParser
{
    /**
     * 处理 @Lockable 注解
     *
     * @param object $object
     * @param string $method
     * @param array $args
     * @param \Imi\Lock\Annotation\Lockable $lockable
     * @param callable $taskCallable
     * @param callable $afterLock
     * @return mixed
     */
    public function parseLockable($object, $method, $args, $lockable, $taskCallable, $afterLock = null)
    {
        $class = get_parent_class($object);

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
        $locker = App::getBean($type, $this->getLockerId($class, $method, $args, $lockable), $options);

        // afterLock 处理
        $afterLockCallable = $lockableAfterLock = $lockable->afterLock;
        if(is_array($lockableAfterLock) && isset($lockableAfterLock[0]) && '$this' === $lockableAfterLock[0])
        {
            // 用反射实现调用 protected 方法
            $refMethod = ReflectionContainer::getMethodReflection($class, $lockableAfterLock[1]);
            $lockableAfterLock = $refMethod->getClosure($object);
        }
        $result = null;
        if(null !== $lockableAfterLock)
        {
            $afterLockCallable = function() use($lockableAfterLock, &$result){
                $result = $lockableAfterLock();
                return null !== $result;
            };
        }

        if(null !== $afterLock)
        {
            $firstAfterLockCallable = $afterLockCallable;
            $afterLockCallable = function() use($firstAfterLockCallable, $afterLock, &$result){
                if(null !== $firstAfterLockCallable)
                {
                    $result = $firstAfterLockCallable();
                    if(null !== $result)
                    {
                        return true;
                    }
                }
                $result = $afterLock();
                return null !== $result;
            };
        }

        if(!$locker->lock(function() use($taskCallable, &$result){
            // 执行原方法
            $result = $taskCallable();
        }, $afterLockCallable))
        {
            throw new LockFailException(sprintf('Get lock failed, id:%s', $locker->getId()));
        }

        return $result;
    }

    /**
     * 获取ID
     *
     * @param string $class
     * @param string $method
     * @param array $args
     * @param \Imi\Lock\Annotation\Lockable $lockable
     * @return string
     */
    private function getLockerId($class, $method, $args, Lockable $lockable)
    {
        $args = ClassObject::convertArgsToKV($class, $method, $args);
        if(null === $lockable->id)
        {
            return md5(
                $class
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
                $value = ObjectArrayHelper::get($args, $matches[1]);
                if(is_scalar($value))
                {
                    return $value;
                }
                else
                {
                    return md5(serialize($value));
                }
            }, $lockable->id);
        }
    }
}