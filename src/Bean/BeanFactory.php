<?php
namespace Imi\Bean;

use Imi\Config;
use Imi\Worker;
use Imi\Util\Imi;
use Imi\Util\File;
use Imi\RequestContext;
use Imi\Util\ClassObject;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Bean\Annotation\AnnotationManager;
use \Swoole\Coroutine;
use Imi\Aop\PointCutType;

abstract class BeanFactory
{
    /**
     * 协程文件锁集合
     *
     * @var array
     */
    public static $fileLockMap = [];

    /**
     * 实例化
     * @param string $class
     * @param mixed ...$args
     * @return mixed
     */
    public static function newInstance($class, ...$args)
    {
        $isCurrentLock = false;
        try{
            $cacheFileName = static::getCacheFileName($class);

            if(null === Worker::getWorkerID())
            {
                if(!is_file($cacheFileName))
                {
                    $tpl = static::getTpl(new \ReflectionClass($class));
                    $path = dirname($cacheFileName);
                    if(!is_dir($path))
                    {
                        File::createDir($path);
                    }
                    file_put_contents($cacheFileName, '<?php ' . $tpl);
                }
            }
            else
            {
                if(isset(static::$fileLockMap[$class]))
                {
                    static::$fileLockMap[$class][] = Coroutine::getuid();
                    Coroutine::suspend();
                }
                $isCurrentLock = true;
                static::$fileLockMap[$class] = [];
                if(!is_file($cacheFileName))
                {
                    $tpl = static::getTpl(new \ReflectionClass($class));
                    $path = dirname($cacheFileName);
                    if(!is_dir($path))
                    {
                        File::createDir($path);
                    }
                    file_put_contents($cacheFileName, '<?php ' . $tpl);
                }
            }

            $object = include $cacheFileName;
        }
        finally{
            if($isCurrentLock && isset(static::$fileLockMap[$class]))
            {
                $coids = static::$fileLockMap[$class];
                static::$fileLockMap[$class] = null;
                foreach($coids as $coid)
                {
                    Coroutine::resume($coid);
                }
            }
        }
        return $object;
    }

    /**
     * 获取类缓存文件名
     *
     * @param string $className
     * @return string
     */
    private static function getCacheFileName($className)
    {
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if(null === ($workerID = Worker::getWorkerID()))
        {
            return Imi::getImiClassCachePath($fileName);
        }
        else
        {
            return Imi::getWorkerClassCachePathByWorkerID($workerID, $fileName);
        }
    }

    /**
     * 获取类模版
     * @param \ReflectionClass $ref
     * @param mixed ...$args
     * @return string
     */
    private static function getTpl($ref)
    {
        $class = $ref->getName();
        $methodsTpl = static::getMethodsTpl($ref, $class);
        $construct = '';
        $constructMethod = $ref->getConstructor();
        if(null !== $constructMethod)
        {
            $paramsTpls = static::getMethodParamTpls($constructMethod);
            $constructDefine = $paramsTpls['define'];
            $construct = "parent::__construct({$paramsTpls['call']});";
            if(static::hasAop($ref, '__construct'))
            {
                $aopConstruct = <<<TPL
        \$__args__ = func_get_args();
        {$paramsTpls['set_args']}
        \$__result__ = \$this->beanProxy->call(
            '__construct',
            function({$paramsTpls['define']}){
                \$__args__ = func_get_args();
                {$paramsTpls['set_args']}
                return parent::__construct(...\$__args__);
            },
            \$__args__
        );

TPL;
            }
            else
            {
                $aopConstruct = $construct;
            }
        }
        else
        {
            $constructDefine = '...$args';
            $aopConstruct = '';
        }
        $aopConstruct .= <<<TPL
\$this->beanProxy->injectProps();
TPL;
        if($ref->hasMethod('__init'))
        {
            if(isset($paramsTpls['call']))
            {
                $aopConstruct .= <<<TPL
        \$this->__init({$paramsTpls['call']});
TPL;
            }
            else
            {
                $aopConstruct .= <<<TPL
        \$this->__init();
TPL;
            }
        }
        // 匿名类模版定义
        $tpl = <<<TPL
return new class(...\$args) extends \\{$class}
{
    protected \$beanProxy;

    public function __construct({$constructDefine})
    {
        \$this->beanProxy = new \Imi\Bean\BeanProxy(\$this);
        {$aopConstruct}
    }

{$methodsTpl}
};
TPL;
        return $tpl;
    }

    /**
     * 获取方法模版
     * @param \ReflectionClass $ref
     * @param string $class
     * @return string
     */
    private static function getMethodsTpl($ref, $class)
    {
        $tpl = '';
        foreach($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            if($method->isStatic() || '__construct' === $method->name || $method->isFinal() || !static::hasAop($ref, $method))
            {
                continue;
            }
            $paramsTpls = static::getMethodParamTpls($method);
            $methodReturnType = static::getMethodReturnType($method);
            $returnsReference = $method->returnsReference() ? '&' : '';
            $tpl .= <<<TPL
    public function {$returnsReference}{$method->name}({$paramsTpls['define']}){$methodReturnType}
    {
        \$__args__ = func_get_args();
        {$paramsTpls['set_args']}
        \$__result__ = \$this->beanProxy->call(
            '{$method->name}',
            function({$paramsTpls['define']}){
                \$__args__ = func_get_args();
                {$paramsTpls['set_args']}
                return parent::{$method->name}(...\$__args__);
            },
            \$__args__
        );
        return \$__result__;
    }

TPL;
        }
        return $tpl;
    }

    /**
     * 获取方法参数模版们
     * @param \ReflectionClass $ref
     * @return string
     */
    private static function getMethodParamTpls(\ReflectionMethod $method)
    {
        $result = [
            'args'      => [],
            'define'    => [],
            'call'      => [],
            'set_args'  => '',
        ];
        foreach($method->getParameters() as $i => $param)
        {
            // 数组参数，支持可变传参
            if(!$param->isVariadic())
            {
                $result['args'][] = static::getMethodParamArgsTpl($param);
            }
            // 方法参数定义
            $result['define'][] = static::getMethodParamDefineTpl($param);
            // 调用传参
            $result['call'][] = static::getMethodParamCallTpl($param);
            // 引用传参
            if($param->isPassedByReference())
            {
                $result['set_args'] .= '$__args__[' . $i . '] = &$' . $param->name . ';';
            }
        }
        foreach($result as $key => &$item)
        {
            if(is_array($item))
            {
                $item = implode(',', $item);
            }
        }
        // 调用如果参数为空处理
        if('' === $result['call'])
        {
            $result['call'] = '...func_get_args()';
        }
        return $result;
    }

    /**
     * 获取方法参数模版
     * @param \ReflectionParameter $param
     * @return string
     */
    private static function getMethodParamArgsTpl(\ReflectionParameter $param)
    {
        $reference = $param->isPassedByReference() ? '&' : '';
        return $reference . '$' . $param->name;
    }

    /**
     * 获取方法参数定义模版
     * @param \ReflectionParameter $param
     * @return string
     */
    private static function getMethodParamDefineTpl(\ReflectionParameter $param)
    {
        $result = '';
        // 类型
        $paramType = $param->getType();
        if(null !== $paramType && $param->allowsNull())
        {
            $paramType = '?' . $paramType;
        }
        $result .= null === $paramType ? '' : ((string)$paramType . ' ');
        if($param->isPassedByReference())
        {
            // 引用传参
            $result .= '&';
        }
        else if($param->isVariadic())
        {
            // 可变参数...
            $result .= '...';
        }
        // $参数名
        $result .= '$' . $param->name;
        // 默认值
        if($param->isOptional() && !$param->isVariadic())
        {
            if($param->isDefaultValueAvailable())
            {
                $result .= ' = ' . var_export($param->getDefaultValue(), true);
            }
            else
            {
                $result .= ' = null';
            }
        }
        return $result;
    }

    /**
     * 获取方法参数调用模版
     * @param \ReflectionParameter $param
     * @return string
     */
    private static function getMethodParamCallTpl(\ReflectionParameter $param)
    {
        return ($param->isVariadic() ? '...' : '') . '$' . $param->name;
    }

    /**
     * 获取方法返回值模版
     * @param \ReflectionMethod $method
     * @return string
     */
    private static function getMethodReturnType(\ReflectionMethod $method)
    {
        if(!$method->hasReturnType())
        {
            return '';
        }
        return ' : ' . $method->getReturnType();
    }

    /**
     * 获取对象类名
     * @param string|object $object
     * @return string
     */
    public static function getObjectClass($object)
    {
        if(is_object($object))
        {
            if(ClassObject::isAnymous($object))
            {
                return get_parent_class($object);
            }
            else
            {
                return get_class($object);
            }
        }
        else
        {
            return (string)$object;
        }
    }

    /**
     * 是否有Aop注入当前方法
     *
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return boolean
     */
    private static function hasAop($class, $method)
    {
        $aspects = AnnotationManager::getAnnotationPoints(Aspect::class);
        $className = $class->getName();
        if('__construct' === $method)
        {
            foreach($aspects as $item)
            {
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item['class'], PointCut::class);
                foreach($pointCutsSet as $methodName => $pointCuts)
                {
                    foreach($pointCuts as $pointCut)
                    {
                        switch($pointCut->type)
                        {
                            case PointCutType::CONSTRUCT:
                                // 构造方法
                                foreach($pointCut->allow as $allowItem)
                                {
                                    if(Imi::checkRuleMatch($allowItem, $className))
                                    {
                                        return true;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION_CONSTRUCT:
                                // 注解构造方法
                                $classAnnotations = AnnotationManager::getClassAnnotations($className);
                                foreach($pointCut->allow as $allowItem)
                                {
                                    foreach($classAnnotations as $annotation)
                                    {
                                        if($annotation instanceof $allowItem)
                                        {
                                            return true;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        else
        {
            $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method->getName());
            foreach($aspects as $item)
            {
                // 判断是否属于当前类的切面
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item['class'], PointCut::class);
                foreach($pointCutsSet as $methodName => $pointCuts)
                {
                    foreach($pointCuts as $pointCut)
                    {
                        switch($pointCut->type)
                        {
                            case PointCutType::METHOD:
                                foreach($pointCut->allow as $allowItem)
                                {
                                    if(Imi::checkClassRule($allowItem, $className))
                                    {
                                        return true;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION:
                                foreach($pointCut->allow as $allowItem)
                                {
                                    foreach($methodAnnotations as $annotation)
                                    {
                                        if($annotation instanceof $allowItem)
                                        {
                                            return true;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        return false;
    }
}