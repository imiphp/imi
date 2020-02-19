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
use Imi\Bean\Parser\PartialParser;

abstract class BeanFactory
{
    /**
     * 计数器
     *
     * @var integer
     */
    private static $counter = 0;

    /**
     * 类名映射
     *
     * @var array
     */
    private static $classNameMap = [];

    /**
     * 实例化
     * @param string $class
     * @param mixed ...$args
     * @return mixed
     */
    public static function newInstance($class, ...$args)
    {
        if(!isset(static::$classNameMap[$class]))
        {
            $ref = new \ReflectionClass($class);
            $className = static::getNewClassName($ref->getShortName());
            $tpl = static::getTpl($ref, $className);
            Imi::eval($tpl);
            static::$classNameMap[$class] = $className;
        }
        $object = new static::$classNameMap[$class](...$args);
        static::initInstance($object, $args);
        return $object;
    }

    /**
     * 实例化，但不初始化
     *
     * @param string $class
     * @param mixed ...$args
     * @return void
     */
    public static function newInstanceNoInit($class, ...$args)
    {
        if(!isset(static::$classNameMap[$class]))
        {
            $ref = new \ReflectionClass($class);
            $className = static::getNewClassName($ref->getShortName());
            $tpl = static::getTpl($ref, $className);
            Imi::eval($tpl);
            static::$classNameMap[$class] = $className;
        }
        return new static::$classNameMap[$class](...$args);
    }

    /**
     * 初始化Bean对象
     *
     * @param object $object
     * @param array $args
     * @param \ReflectionClass $ref
     * @return void
     */
    public static function initInstance($object, $args = [])
    {
        $ref = new \ReflectionClass($object);
        $beanProxy = $ref->getProperty('beanProxy');
        $beanProxy->setAccessible(true);
        $beanProxy->getValue($object)
                  ->injectProps($object);
        if($ref->hasMethod('__init'))
        {
            $ref->getMethod('__init')->invoke($object, ...$args);
        }
    }

    /**
     * 获取新的类名
     *
     * @return string
     */
    private static function getNewClassName($className)
    {
        return $className . '__Bean__' . (++static::$counter);
    }

    /**
     * 获取类模版
     * @param \ReflectionClass $ref
     * @param string $newClassName
     * @return string
     */
    private static function getTpl($ref, $newClassName)
    {
        $class = $ref->getName();
        $methodsTpl = static::getMethodsTpl($ref);
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
            \$this,
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
        // partial 处理
        $classes = [$class];
        $parentClass = $ref;
        while($parentClass = $parentClass->getParentClass())
        {
            $classes[] = $parentClass->getName();
        }
        if(isset($classes[1]))
        {
            $classes = array_reverse(array_unique($classes));
        }
        $partialData = PartialParser::getInstance()->getData();
        $traits = [];
        foreach($classes as $currentClass)
        {
            if(isset($partialData[$currentClass]))
            {
                $traits[] = $partialData[$currentClass];
            }
        }
        if($traits)
        {
            $traits = array_unique(array_merge(...$traits));
            $traitsTpl = 'use ' . implode(',', $traits) . ';';
        }
        else
        {
            $traitsTpl = '';
        }

        $parentClone = $ref->hasMethod('__clone') ? "parent::__clone();" : '';
        // 类模版定义
        $tpl = <<<TPL
class {$newClassName} extends {$class} implements \Imi\Bean\IBean
{
    {$traitsTpl}

    protected \$beanProxy;

    public function __construct({$constructDefine})
    {
        \$this->beanProxy = new \Imi\Bean\BeanProxy(\$this);
        {$aopConstruct}
    }

    public function __clone()
    {
        \$this->beanProxy = new \Imi\Bean\BeanProxy(\$this);
        \$this->beanProxy->injectProps(\$this);
        {$parentClone}
    }
{$methodsTpl}
}
TPL;
        return $tpl;
    }

    /**
     * 获取方法模版
     * @param \ReflectionClass $ref
     * @return string
     */
    private static function getMethodsTpl($ref)
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
            \$this,
            '{$method->name}',
            function({$paramsTpls['define']}){
                \$__args__ = func_get_args();
                {$paramsTpls['set_args']}
                return parent::{$method->name}(...\$__args__);
            },
            \$__args__
        );
        {$paramsTpls['set_args_back']}
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
        $args = $define = $call = [];
        $setArgs = $setArgsBack = '';
        $result = [
            'args'          => &$args,
            'define'        => &$define,
            'call'          => &$call,
            'set_args'      => &$setArgs,
            'set_args_back' => &$setArgsBack,
        ];
        foreach($method->getParameters() as $i => $param)
        {
            // 数组参数，支持可变传参
            if(!$param->isVariadic())
            {
                $args[] = static::getMethodParamArgsTpl($param);
            }
            // 方法参数定义
            $define[] = static::getMethodParamDefineTpl($param);
            // 调用传参
            $call[] = static::getMethodParamCallTpl($param);
            // 引用传参
            if($param->isPassedByReference())
            {
                $setArgs .= '$__args__[' . $i . '] = &$' . $param->name . ';';
                $setArgsBack .= '$' . $param->name . ' = $__args__[' . $i . '];';
            }
        }
        foreach($result as &$item)
        {
            if(is_array($item))
            {
                $item = implode(',', $item);
            }
        }
        // 调用如果参数为空处理
        if('' === $call)
        {
            $call = '...func_get_args()';
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
        if($paramType)
        {
            $paramType = $paramType->getName();
        }
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
        return ' : ' . $method->getReturnType()->getName();
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
            if($object instanceof IBean)
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
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item->getClass(), PointCut::class);
                foreach($pointCutsSet as $pointCuts)
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
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item->getClass(), PointCut::class);
                foreach($pointCutsSet as $pointCuts)
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