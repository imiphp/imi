<?php

declare(strict_types=1);

namespace Imi\RequestContextProxy;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\RequestContext;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;

/**
 * 请求上下文代理基类.
 */
abstract class BaseRequestContextProxy
{
    /**
     * 请求上下文代理缓存.
     *
     * @var \Imi\RequestContextProxy\Annotation\RequestContextProxy[]
     */
    protected static $cache = [];

    /**
     * 获取实例.
     *
     * @return mixed
     */
    public static function __getProxyInstance()
    {
        $cache = &self::$cache;
        if (isset($cache[static::class]))
        {
            /** @var RequestContextProxy $cacheItem */
            $cacheItem = $cache[static::class];
        }
        else
        {
            /** @var RequestContextProxy[] $annotations */
            $annotations = AnnotationManager::getClassAnnotations(static::class, RequestContextProxy::class);
            if (!isset($annotations[0]))
            {
                throw new \RuntimeException(sprintf('Class %s not found @RequestContextProxy Annotation', static::class));
            }
            $cache[static::class] = $cacheItem = $annotations[0];
        }

        return RequestContext::get($cacheItem->name);
    }

    /**
     * 绑定代理.
     *
     * @param string      $proxyClass
     * @param string      $name
     * @param string|null $bindClass
     *
     * @return void
     */
    public static function __bindProxy(string $proxyClass, string $name, ?string $bindClass = null)
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
     *
     * @return void
     */
    public static function __clearCache()
    {
        self::$cache = [];
    }

    public function __call($name, $arguments)
    {
        return static::__getProxyInstance()->$name(...$arguments);
    }

    public static function __callStatic($method, $arguments)
    {
        return static::__getProxyInstance()->$method(...$arguments);
    }
}
