<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\RequestContext;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\Util\Traits\TBeanRealClass;

/**
 * 请求上下文代理基类.
 */
abstract class BaseRequestContextProxy
{
    use TBeanRealClass;

    /**
     * 请求上下文代理缓存.
     *
     * @var \Imi\RequestContextProxy\Annotation\RequestContextProxy[]
     */
    protected static array $cache = [];

    /**
     * 获取请求上下文中的实例.
     */
    public static function __getProxyInstance(): object
    {
        $cache = &self::$cache;
        $currentClass = self::__getRealClassName();
        if (isset($cache[$currentClass]))
        {
            /** @var RequestContextProxy $cacheItem */
            $cacheItem = $cache[$currentClass];
        }
        else
        {
            $cacheItem = AnnotationManager::getClassAnnotations($currentClass, RequestContextProxy::class, true, true);
            if (!$cacheItem)
            {
                throw new \RuntimeException(sprintf('Class %s not found @RequestContextProxy Annotation', $currentClass));
            }
            $cache[$currentClass] = $cacheItem;
        }

        return RequestContext::get($cacheItem->name);
    }

    /**
     * 设置请求上下文中的实例.
     *
     * @param mixed $instance
     */
    public static function __setProxyInstance($instance): void
    {
        $cache = &self::$cache;
        $currentClass = self::__getRealClassName();
        if (isset($cache[$currentClass]))
        {
            /** @var RequestContextProxy $cacheItem */
            $cacheItem = $cache[$currentClass];
        }
        else
        {
            $cacheItem = AnnotationManager::getClassAnnotations($currentClass, RequestContextProxy::class, true, true);
            if (!$cacheItem)
            {
                throw new \RuntimeException(sprintf('Class %s not found @RequestContextProxy Annotation', $currentClass));
            }
            $cache[$currentClass] = $cacheItem;
        }
        RequestContext::set($cacheItem->name, $instance);
    }

    /**
     * 绑定代理.
     */
    public static function __bindProxy(string $proxyClass, string $name, ?string $bindClass = null): void
    {
        $cache = &self::$cache;
        if (isset($cache[$proxyClass]))
        {
            throw new \RuntimeException(sprintf('RequestContextProxy %s already exists', $proxyClass));
        }
        $cache[$proxyClass] = new RequestContextProxy([
            'class' => $bindClass,
            'name'  => $name,
        ]);
    }

    /**
     * 清除代理缓存.
     */
    public static function __clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return static::__getProxyInstance()->$name(...$arguments);
    }

    /**
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return static::__getProxyInstance()->$method(...$arguments);
    }
}
