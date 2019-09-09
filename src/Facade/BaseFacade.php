<?php
namespace Imi\Facade;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Facade\Annotation\Facade;
use Imi\RequestContext;

/**
 * 门面基类
 */
abstract class BaseFacade
{
    /**
     * 门面缓存
     *
     * @var \Imi\Facade\Annotation\Facade[]
     */
    protected static $cache = [];

    /**
     * 获取实例
     *
     * @return mixed
     */
    public static function __getFacadeInstance()
    {
        if(!isset(self::$cache[static::class]))
        {
            $annotations = AnnotationManager::getClassAnnotations(static::class, Facade::class);
            if(!isset($annotations[0]))
            {
                throw new \RuntimeException(sprintf('Class %s not found @Facade Annotation', static::class));
            }
            self::$cache[static::class] = $annotations[0];
        }
        if(self::$cache[static::class]->request)
        {
            return RequestContext::getBean(self::$cache[static::class]->class, self::$cache[static::class]->args);
        }
        else
        {
            return App::getBean(self::$cache[static::class]->class, self::$cache[static::class]->args);
        }
    }

    /**
     * 绑定门面
     *
     * @param string $facadeClass
     * @param string $bindClass
     * @param mixed ...$args
     * @return void
     */
    public static function __bindFacade($facadeClass, $bindClass = null, ...$args)
    {
        if(isset(self::$cache[$facadeClass]))
        {
            throw new \RuntimeException(sprintf('Facade %s already exists', $facadeClass));
        }
        self::$cache[$facadeClass] = new Facade([
            'class' =>  $bindClass,
            'args'  =>  $args,
        ]);
    }

    /**
     * 清除门面缓存
     *
     * @return void
     */
    public static function __clearCache()
    {
        self::$cache = [];
    }

    public static function __callStatic($method, $arguments)
    {
        return static::__getFacadeInstance()->$method(...$arguments);
    }

}
