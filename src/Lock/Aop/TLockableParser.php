<?php

declare(strict_types=1);

namespace Imi\Lock\Aop;

use Imi\App;
use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionContainer;
use Imi\Config;
use Imi\Lock\Annotation\Lockable;
use Imi\Lock\Exception\LockFailException;
use Imi\Lock\Handler\ILockHandler;
use Imi\Lock\Lock;
use Imi\Util\ClassObject;
use Imi\Util\ObjectArrayHelper;

trait TLockableParser
{
    #[Lockable]
    public function parseLockable(object $object, string $method, array $args, Lockable $lockable, callable $taskCallable, ?callable $afterLock = null): mixed
    {
        $class = BeanFactory::getObjectClass($object);

        // 加载配置
        if (null !== $lockable->id && $lockable->useConfig)
        {
            $options = Lock::getOptions();

            if (!empty($config = $options[$lockable->id] ?? null))
            {
                if (!empty($config->class))
                {
                    $lockable->type = $config->class;
                }

                $lockable->options = array_merge($config->options, $lockable->options);
            }
        }

        // Lock 类型
        if (null === $lockable->type)
        {
            $type = Config::get('@currentServer.lock.defaultType', 'RedisLock');
        }
        else
        {
            $type = $lockable->type;
        }

        // options
        $options = $lockable->options;
        $options['waitTimeout'] = $lockable->waitTimeout ?? $options['waitTimeout'] ?? 3000;
        $options['lockExpire'] = $lockable->lockExpire ?? $options['lockExpire'] ?? 3000;
        $options['timeoutException'] = $lockable->timeoutException ?? $options['timeoutException'] ?? false;
        $options['unlockException'] = $lockable->unlockException ?? $options['unlockException'] ?? false;

        // Lock 对象
        /** @var ILockHandler $locker */
        $locker = App::newInstance($type, $this->getLockerId($class, $method, $args, $lockable), $options);

        // afterLock 处理
        $afterLockCallable = $lockableAfterLock = $lockable->afterLock;
        if (\is_array($lockableAfterLock) && isset($lockableAfterLock[0]) && '$this' === $lockableAfterLock[0])
        {
            // 用反射实现调用 protected 方法
            $refMethod = ReflectionContainer::getMethodReflection($class, $lockableAfterLock[1]);
            $lockableAfterLock = $refMethod->getClosure($object);
        }
        $result = null;
        if (null !== $lockableAfterLock)
        {
            $afterLockCallable = static function () use ($lockableAfterLock, &$result): bool {
                $result = $lockableAfterLock();

                return null !== $result;
            };
        }

        if (null !== $afterLock)
        {
            $firstAfterLockCallable = $afterLockCallable;
            $afterLockCallable = static function () use ($firstAfterLockCallable, $afterLock, &$result): bool {
                if (null !== $firstAfterLockCallable)
                {
                    $result = $firstAfterLockCallable();
                    if (null !== $result)
                    {
                        return true;
                    }
                }
                $result = $afterLock();

                return null !== $result;
            };
        }

        if (!$locker->lock(static function () use ($taskCallable, &$result): void {
            // 执行原方法
            $result = $taskCallable();
        }, $afterLockCallable))
        {
            throw new LockFailException(sprintf('Get lock failed, id:%s', $locker->getId()));
        }

        return $result;
    }

    /**
     * 获取ID.
     */
    private function getLockerId(string $class, string $method, array $args, Lockable $lockable): string
    {
        $args = ClassObject::convertArgsToKV($class, $method, $args);
        if (null === $lockable->id)
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
            return preg_replace_callback('/\{([^\}]+)\}/', static function (array $matches) use ($args): string {
                $value = ObjectArrayHelper::get($args, $matches[1]);
                if (\is_scalar($value))
                {
                    return (string) $value;
                }
                else
                {
                    return md5(serialize($value));
                }
            }, (string) $lockable->id);
        }
    }
}
