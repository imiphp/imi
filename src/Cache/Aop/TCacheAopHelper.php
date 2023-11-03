<?php

declare(strict_types=1);

namespace Imi\Cache\Aop;

use Imi\Aop\JoinPoint;
use Imi\Bean\BeanFactory;
use Imi\Cache\Annotation\Cacheable;
use Imi\Cache\Annotation\CacheEvict;
use Imi\Cache\Annotation\CachePut;
use Imi\Util\ObjectArrayHelper;

trait TCacheAopHelper
{
    /**
     * 获取缓存key.
     */
    protected function getKey(JoinPoint $joinPoint, array $args, Cacheable|CacheEvict|CachePut $cacheable): string
    {
        if ('' === $cacheable->key)
        {
            return md5(
                BeanFactory::getObjectClass($joinPoint->getTarget())
                . '::'
                . $joinPoint->getMethod()
                . '('
                . serialize($args)
                . ')'
            );
        }
        else
        {
            return preg_replace_callback('/\{([^\}]+)\}/', static function (array $matches) use ($args, $cacheable): string {
                $argName = $matches[1];
                if (':args' === $argName)
                {
                    return ($cacheable->hashMethod)(serialize($args));
                }
                else
                {
                    $value = ObjectArrayHelper::get($args, $argName);
                    if (\is_scalar($value))
                    {
                        return (string) $value;
                    }
                    else
                    {
                        return ($cacheable->hashMethod)(serialize($value));
                    }
                }
            }, $cacheable->key);
        }
    }

    /**
     * 获取缓存值
     */
    protected function getValue(CachePut $cachePut, mixed $value): mixed
    {
        if (null === $cachePut->value)
        {
            return $value;
        }

        return ObjectArrayHelper::get($value, $cachePut->value);
    }
}
