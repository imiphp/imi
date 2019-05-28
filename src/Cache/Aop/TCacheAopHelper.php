<?php
namespace Imi\Cache\Aop;

use Imi\Aop\JoinPoint;
use Imi\Bean\BeanFactory;
use Imi\Util\ObjectArrayHelper;
use Imi\Cache\Annotation\CachePut;

trait TCacheAopHelper
{
    /**
     * 获取缓存key
     *
     * @param \Imi\Aop\JoinPoint $joinPoint
     * @param array $args
     * @param \Imi\Cache\Annotation\Cacheable|\Imi\Cache\Annotation\CacheEvict|\Imi\Cache\Annotation\CachePut $cacheable
     * @return string
     */
    protected function getKey(JoinPoint $joinPoint, $args, $cacheable)
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
            return preg_replace_callback('/\{([^\}]+)\}/', function($matches) use($args, $cacheable){
                $argName = $matches[1];
                if(':args' === $argName)
                {
                    return ($cacheable->hashMethod)(serialize($args));
                }
                else
                {
                    $value = ObjectArrayHelper::get($args, $argName);
                    if(is_scalar($value))
                    {
                        return $value;
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
     *
     * @param \Imi\Cache\Annotation\CachePut $cachePut
     * @param mixed $value
     * @return mixed
     */
    protected function getValue(CachePut $cachePut, $value)
    {
        if(null === $cachePut->value)
        {
            return $value;
        }
        return ObjectArrayHelper::get($value, $cachePut->value);
    }
}