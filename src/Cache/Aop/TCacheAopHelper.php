<?php
namespace Imi\Cache\Aop;

use Imi\Aop\JoinPoint;
use Imi\Bean\BeanFactory;
use Imi\Util\ObjectArrayHelper;
use Imi\Cache\Annotation\CachePut;

trait TCacheAopHelper
{
    /**
     * 获取方法参数数组，key=>value
     *
     * @param JoinPoint $joinPoint
     * @return array
     */
    protected function getArgs(JoinPoint $joinPoint)
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