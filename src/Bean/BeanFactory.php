<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\PointCutType;
use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Parser\PartialParser;
use Imi\Util\Imi;

class BeanFactory
{
    /**
     * 计数器.
     *
     * @var int
     */
    private static int $counter = 0;

    /**
     * 类名映射.
     *
     * @var array
     */
    private static array $classNameMap = [];

    private function __construct()
    {
    }

    /**
     * 实例化.
     *
     * @param string $class
     * @param mixed  ...$args
     *
     * @return object
     */
    public static function newInstance(string $class, ...$args): object
    {
        $object = self::newInstanceNoInit($class, ...$args);
        static::initInstance($object, $args);

        return $object;
    }

    /**
     * 实例化，但不初始化.
     *
     * @param string $class
     * @param mixed  ...$args
     *
     * @return object
     */
    public static function newInstanceNoInit(string $class, ...$args): object
    {
        $classNameMap = &static::$classNameMap;
        if (isset($classNameMap[$class]))
        {
            $className = $classNameMap[$class];
        }
        else
        {
            $ref = ReflectionContainer::getClassReflection($class);
            if (App::get(BeanContexts::FIXED_EVAL_NAME, false))
            {
                static::parseEvalName($class, $fileName, $className);
                if (is_file($fileName))
                {
                    require $fileName;
                }
                else
                {
                    $tpl = static::getTpl($ref, $className);
                    Imi::eval($tpl, $fileName, false);
                }
            }
            else
            {
                $className = static::getNewClassName($ref->getShortName());
                $tpl = static::getTpl($ref, $className);
                Imi::eval($tpl);
            }
            $classNameMap[$class] = $className;
        }

        return new $className(...$args);
    }

    /**
     * 初始化Bean对象
     *
     * @param object $object
     * @param array  $args
     *
     * @return void
     */
    public static function initInstance(object $object, array $args = [])
    {
        $ref = ReflectionContainer::getClassReflection(\get_class($object));
        BeanProxy::injectProps($object, self::getObjectClass($object));
        if ($ref->hasMethod('__init'))
        {
            $ref->getMethod('__init')->invoke($object, ...$args);
        }
    }

    /**
     * 获取新的类名.
     *
     * @return string
     */
    private static function getNewClassName(string $className): string
    {
        return $className . '__Bean__' . (++static::$counter);
    }

    /**
     * 获取类模版.
     *
     * @param \ReflectionClass $ref
     * @param string           $newClassName
     *
     * @return string
     */
    private static function getTpl(\ReflectionClass $ref, string $newClassName): string
    {
        $class = $ref->getName();
        $methodsTpl = static::getMethodsTpl($ref);
        $construct = '';
        $constructMethod = $ref->getConstructor();
        if (null !== $constructMethod)
        {
            $paramsTpls = static::getMethodParamTpls($constructMethod);
            $constructDefine = $paramsTpls['define'];
            $construct = "parent::__construct({$paramsTpls['call']});";
            if (static::hasAop($ref, '__construct'))
            {
                $aopConstruct = <<<TPL
        \$__args__ = func_get_args();
        {$paramsTpls['set_args']}
        \$__result__ = \Imi\Bean\BeanProxy::call(
            \$this,
            parent::class,
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
        $classes = class_parents($class);
        if (isset($classes[1]))
        {
            $classes = array_reverse($classes);
        }
        $classes[] = $class;

        $partialData = PartialParser::getInstance()->getData();
        $traits = [];
        foreach ($classes as $currentClass)
        {
            if (isset($partialData[$currentClass]))
            {
                $traits[] = $partialData[$currentClass];
            }
        }
        if ($traits)
        {
            $traits = array_unique(array_merge(...$traits));
            $traitsTpl = 'use ' . implode(',', $traits) . ';';
        }
        else
        {
            $traitsTpl = '';
        }

        $parentClone = $ref->hasMethod('__clone') ? 'parent::__clone();' : '';
        // 类模版定义
        $tpl = <<<TPL
class {$newClassName} extends {$class} implements \Imi\Bean\IBean
{
    {$traitsTpl}

    public function __construct({$constructDefine})
    {
        {$aopConstruct}
    }

    public function __clone()
    {
        \Imi\Bean\BeanProxy::injectProps(\$this, parent::class, true);
        {$parentClone}
    }
{$methodsTpl}
}
\Imi\Bean\BeanProxy::init({$class}::class);
TPL;

        return $tpl;
    }

    /**
     * 获取方法模版.
     *
     * @param \ReflectionClass $ref
     *
     * @return string
     */
    private static function getMethodsTpl(\ReflectionClass $ref): string
    {
        $tpl = '';
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method)
        {
            $methodName = $method->name;
            if ($method->isStatic() || '__construct' === $methodName || $method->isFinal() || !static::hasAop($ref, $method->getName()))
            {
                continue;
            }
            $paramsTpls = static::getMethodParamTpls($method);
            $methodReturnType = static::getMethodReturnType($method);
            $returnsReference = $method->returnsReference() ? '&' : '';
            $tpl .= <<<TPL
    public function {$returnsReference}{$methodName}({$paramsTpls['define']}){$methodReturnType}
    {
        \$__args__ = func_get_args();
        {$paramsTpls['set_args']}
        \$__result__ = \Imi\Bean\BeanProxy::call(
            \$this,
            parent::class,
            '{$methodName}',
            function({$paramsTpls['define']}){
                \$__args__ = func_get_args();
                {$paramsTpls['set_args']}
                return parent::{$methodName}(...\$__args__);
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
     * 获取方法参数模版们.
     *
     * @param \ReflectionClass $ref
     *
     * @return array
     */
    private static function getMethodParamTpls(\ReflectionMethod $method): array
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
        foreach ($method->getParameters() as $i => $param)
        {
            // 数组参数，支持可变传参
            if (!$param->isVariadic())
            {
                $args[] = static::getMethodParamArgsTpl($param);
            }
            // 方法参数定义
            $define[] = static::getMethodParamDefineTpl($param);
            // 调用传参
            $call[] = static::getMethodParamCallTpl($param);
            // 引用传参
            if ($param->isPassedByReference())
            {
                $paramName = $param->name;
                $setArgs .= '$__args__[' . $i . '] = &$' . $paramName . ';';
                $setArgsBack .= '$' . $paramName . ' = $__args__[' . $i . '];';
            }
        }
        foreach ($result as &$item)
        {
            if (\is_array($item))
            {
                $item = implode(',', $item);
            }
        }
        // 调用如果参数为空处理
        if ('' === $call)
        {
            $call = '...func_get_args()';
        }

        return $result;
    }

    /**
     * 获取方法参数模版.
     *
     * @param \ReflectionParameter $param
     *
     * @return string
     */
    private static function getMethodParamArgsTpl(\ReflectionParameter $param): string
    {
        $reference = $param->isPassedByReference() ? '&' : '';

        return $reference . '$' . $param->name;
    }

    /**
     * 获取方法参数定义模版.
     *
     * @param \ReflectionParameter $param
     *
     * @return string
     */
    private static function getMethodParamDefineTpl(\ReflectionParameter $param): string
    {
        $result = '';
        // 类型
        $paramType = $param->getType();
        if ($paramType)
        {
            $paramType = $paramType->getName();
        }
        if (null !== $paramType && $param->allowsNull())
        {
            $paramType = '?' . $paramType;
        }
        $result .= null === $paramType ? '' : ((string) $paramType . ' ');
        if ($param->isPassedByReference())
        {
            // 引用传参
            $result .= '&';
        }
        elseif ($param->isVariadic())
        {
            // 可变参数...
            $result .= '...';
        }
        // $参数名
        $result .= '$' . $param->name;
        // 默认值
        if ($param->isDefaultValueAvailable())
        {
            $result .= ' = ' . var_export($param->getDefaultValue(), true);
        }

        return $result;
    }

    /**
     * 获取方法参数调用模版.
     *
     * @param \ReflectionParameter $param
     *
     * @return string
     */
    private static function getMethodParamCallTpl(\ReflectionParameter $param): string
    {
        return ($param->isVariadic() ? '...' : '') . '$' . $param->name;
    }

    /**
     * 获取方法返回值模版.
     *
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    private static function getMethodReturnType(\ReflectionMethod $method): string
    {
        if (!$method->hasReturnType())
        {
            return '';
        }
        $returnType = $method->getReturnType();

        return ': ' . ($returnType->allowsNull() ? '?' : '') . $returnType->getName();
    }

    /**
     * 获取对象类名.
     *
     * @param string|object $object
     *
     * @return string
     */
    public static function getObjectClass($object): string
    {
        if (\is_object($object))
        {
            if ($object instanceof IBean)
            {
                return get_parent_class($object);
            }
            else
            {
                return \get_class($object);
            }
        }
        else
        {
            return (string) $object;
        }
    }

    /**
     * 是否有Aop注入当前方法.
     *
     * @param \ReflectionClass $class
     * @param string           $method
     *
     * @return bool
     */
    private static function hasAop(\ReflectionClass $class, string $method): bool
    {
        $aspects = AnnotationManager::getAnnotationPoints(Aspect::class);
        $className = $class->getName();
        if ('__construct' === $method)
        {
            foreach ($aspects as $item)
            {
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item->getClass(), PointCut::class);
                foreach ($pointCutsSet as $pointCuts)
                {
                    foreach ($pointCuts as $pointCut)
                    {
                        switch ($pointCut->type)
                        {
                            case PointCutType::CONSTRUCT:
                                // 构造方法
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkRuleMatch($allowItem, $className))
                                    {
                                        return true;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION_CONSTRUCT:
                                // 注解构造方法
                                $classAnnotations = AnnotationManager::getClassAnnotations($className);
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    foreach ($classAnnotations as $annotation)
                                    {
                                        if ($annotation instanceof $allowItem)
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
            $methodAnnotations = AnnotationManager::getMethodAnnotations($className, $method);
            foreach ($aspects as $item)
            {
                // 判断是否属于当前类的切面
                $pointCutsSet = AnnotationManager::getMethodsAnnotations($item->getClass(), PointCut::class);
                foreach ($pointCutsSet as $pointCuts)
                {
                    foreach ($pointCuts as $pointCut)
                    {
                        switch ($pointCut->type)
                        {
                            case PointCutType::METHOD:
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    if (Imi::checkClassRule($allowItem, $className))
                                    {
                                        return true;
                                    }
                                }
                                break;
                            case PointCutType::ANNOTATION:
                                foreach ($pointCut->allow as $allowItem)
                                {
                                    foreach ($methodAnnotations as $annotation)
                                    {
                                        if ($annotation instanceof $allowItem)
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

    public static function parseEvalName(string $class, ?string &$fileName, ?string &$className)
    {
        $className = str_replace('\\', '__', $class) . '__Bean__';
        $fileName = Imi::getRuntimePath('classes/' . sha1($class) . '.php');
    }
}
