<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Aop\AopManager;
use Imi\Util\Imi;

class BeanFactory
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 计数器.
     */
    private static int $counter = 0;

    /**
     * 类名映射.
     */
    private static array $classNameMap = [];

    /**
     * 是否启用 bean 文件缓存.
     */
    public static bool $enableFileCache = false;

    /**
     * 实例化.
     *
     * @template T
     *
     * @param class-string<T> $class
     * @param mixed           ...$args
     *
     * @return T
     */
    public static function newInstance(string $class, ...$args)
    {
        $object = self::newInstanceNoInit($class, ...$args);
        static::initInstance($object, $args);

        return $object;
    }

    /**
     * 实例化，但不初始化.
     *
     * @template T
     *
     * @param class-string<T> $class
     * @param mixed           ...$args
     *
     * @return T
     */
    public static function newInstanceNoInit(string $class, ...$args)
    {
        $classNameMap = &self::$classNameMap;
        if (isset($classNameMap[$class]))
        {
            $className = $classNameMap[$class];
        }
        else
        {
            if (self::$enableFileCache)
            {
                static::parseEvalName($class, $fileName, $className);
                if (is_file($fileName))
                {
                    require $fileName;
                }
                else
                {
                    $ref = ReflectionContainer::getClassReflection($class);
                    $tpl = static::getTpl($ref, $className);
                    Imi::eval($tpl, $fileName, false);
                }
            }
            else
            {
                $ref = ReflectionContainer::getClassReflection($class);
                $className = self::getNewClassName($ref->getShortName());
                $tpl = static::getTpl($ref, $className);
                Imi::eval($tpl);
            }
            $classNameMap[$class] = $className;
        }

        /** @var class-string<T> $className */
        return new $className(...$args);
    }

    /**
     * 增强实例化.
     *
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public static function newInstanceEx(string $class, array $args = [])
    {
        $object = self::newInstanceExNoInit($class, $args, $resultArgs);
        static::initInstance($object, $resultArgs);

        return $object;
    }

    /**
     * 增强实例化，但不初始化.
     *
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public static function newInstanceExNoInit(string $class, array $args, ?array &$resultArgs = [])
    {
        $resultArgs = [];
        foreach (ReflectionContainer::getClassReflection($class)->getConstructor()->getParameters() as $param)
        {
            $name = $param->getName();
            if (isset($args[$name]))
            {
                $resultArgs[] = $args[$name];
            }
            elseif ($param->isDefaultValueAvailable())
            {
                $resultArgs[] = $param->getDefaultValue();
            }
            else
            {
                throw new \InvalidArgumentException(sprintf('BeanFactory::newInstanceEx(): %s::__construct() %s not found', $class, $name));
            }
        }

        return self::newInstanceNoInit($class, ...$resultArgs);
    }

    /**
     * 初始化Bean对象
     */
    public static function initInstance(object $object, array $args = []): void
    {
        $class = self::getObjectClass($object);
        BeanProxy::injectProps($object, $class);
        $ref = ReflectionContainer::getClassReflection($class);
        if ($ref->hasMethod('__init'))
        {
            $ref->getMethod('__init')->invoke($object, ...$args);
        }
    }

    /**
     * 获取新的类名.
     */
    private static function getNewClassName(string $className): string
    {
        return $className . '__Bean__' . (++self::$counter);
    }

    /**
     * 获取类模版.
     */
    public static function getTpl(\ReflectionClass $ref, string $newClassName): string
    {
        $class = $ref->getName();
        $methodsTpl = static::getMethodsTpl($ref);
        $constructMethod = $ref->getConstructor();
        if (null !== $constructMethod)
        {
            $paramsTpls = static::getMethodParamTpls($constructMethod);
            if (static::hasAop($ref, '__construct'))
            {
                if ('' === $paramsTpls['set_args'])
                {
                    $constructMethod = <<<TPL
                        public function __construct({$paramsTpls['define']})
                        {
                            \$__args__ = \\func_get_args();
                            \Imi\Bean\BeanProxy::call(
                                \$this,
                                parent::class,
                                '__construct',
                                fn(...\$args) => parent::__construct(...\$args),
                                \$__args__
                            );
                        }
                    TPL;
                }
                else
                {
                    $constructMethod = <<<TPL
                        public function __construct({$paramsTpls['define']})
                        {
                            \$__args__ = \\func_get_args();
                            {$paramsTpls['set_args']}
                            \Imi\Bean\BeanProxy::call(
                                \$this,
                                parent::class,
                                '__construct',
                                function ({$paramsTpls['define']}){
                                    \$__args__ = \\func_get_args();
                                    {$paramsTpls['set_args']}
                                    parent::__construct(...\$__args__);
                                },
                                \$__args__
                            );
                        }
                    TPL;
                }
            }
            elseif ($constructMethod->isProtected())
            {
                $constructMethod = <<<TPL
                public function __construct({$paramsTpls['define']})
                {
                    parent::__construct({$paramsTpls['call']});
                }
                TPL;
            }
            else
            {
                $constructMethod = '';
            }
        }
        else
        {
            $constructMethod = '';
        }
        // partial 处理
        $traits = PartialManager::getClassPartials($class);
        if ($traits)
        {
            $traits = array_unique(isset($traits[1]) ? array_merge(...$traits) : $traits[0]);
            $traitsTpl = 'use ' . implode(', ', $traits) . ';';
        }
        else
        {
            $traitsTpl = '';
        }

        $parentClone = $ref->hasMethod('__clone') ? 'parent::__clone();' : '';
        // @phpstan-ignore-next-line
        $readonly = (\PHP_VERSION_ID >= 80200 && $ref->isReadOnly()) ? 'readonly ' : '';

        // 类模版定义
        return <<<TPL
        declare(strict_types=1);

        {$readonly}class {$newClassName} extends {$class} implements \Imi\Bean\IBean
        {
            {$traitsTpl}

            {$constructMethod}

            public function __clone()
            {
                \Imi\Bean\BeanProxy::injectProps(\$this, parent::class, true);
                {$parentClone}
            }
        {$methodsTpl}
        }
        TPL;
    }

    /**
     * 获取方法模版.
     */
    public static function getMethodsTpl(\ReflectionClass $ref): string
    {
        $tpl = '';
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method)
        {
            $methodName = $method->name;
            if ('__construct' === $methodName || $method->isStatic() || $method->isFinal() || !static::hasAop($ref, $methodName))
            {
                continue;
            }
            $paramsTpls = static::getMethodParamTpls($method);
            $methodReturnType = static::getMethodReturnType($method);
            $returnsReferenceStr = ($returnsReference = $method->returnsReference()) ? '&' : '';
            $returnsReference = $returnsReference ? 'true' : 'false';

            if ('' === $paramsTpls['set_args'])
            {
                $returnContent = $method->hasReturnType() && !ReflectionUtil::isAllowReturnedType($method->getReturnType()) ? '' : 'return ';
                $tpl .= <<<TPL
                    public function {$returnsReferenceStr}{$methodName}({$paramsTpls['define']}){$methodReturnType}
                    {
                        \$__args__ = \\func_get_args();
                        {$returnContent}\Imi\Bean\BeanProxy::call(
                            \$this,
                            parent::class,
                            '{$methodName}',
                            fn {$returnsReferenceStr}(...\$args) => parent::{$methodName}(...\$args),
                            \$__args__,
                            {$returnsReference}
                        );
                    }
                TPL;
            }
            else
            {
                $returnContent = $method->hasReturnType() && !ReflectionUtil::isAllowReturnedType($method->getReturnType()) ? '' : 'return $__result__;';
                $tpl .= <<<TPL
                    public function {$returnsReferenceStr}{$methodName}({$paramsTpls['define']}){$methodReturnType}
                    {
                        \$__args__ = \\func_get_args();
                        {$paramsTpls['set_args']}
                        \$__result__ = \Imi\Bean\BeanProxy::call(
                            \$this,
                            parent::class,
                            '{$methodName}',
                            function {$returnsReferenceStr}({$paramsTpls['define']}){
                                \$__args__ = \\func_get_args();
                                {$paramsTpls['set_args']}
                                return parent::{$methodName}(...\$__args__);
                            },
                            \$__args__,
                            {$returnsReference}
                        );
                        {$paramsTpls['set_args_back']}
                        {$returnContent}
                    }

                TPL;
            }
        }

        return $tpl;
    }

    /**
     * 获取方法参数模版们.
     */
    public static function getMethodParamTpls(\ReflectionMethod $method): array
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
        // 调用如果参数为空处理
        if (!$call)
        {
            $call = ['...\func_get_args()'];
        }
        foreach ($result as &$item)
        {
            if (\is_array($item))
            {
                $item = implode(', ', $item);
            }
        }

        return $result;
    }

    /**
     * 获取方法参数模版.
     */
    public static function getMethodParamArgsTpl(\ReflectionParameter $param): string
    {
        return ($param->isPassedByReference() ? '&' : '') . '$' . $param->name;
    }

    /**
     * 获取方法参数定义模版.
     */
    public static function getMethodParamDefineTpl(\ReflectionParameter $param): string
    {
        $result = '';
        // 类型
        $paramType = $param->getType();
        if ($paramType)
        {
            $paramType = ReflectionUtil::getTypeCode($paramType, $param->getDeclaringClass()->getName());
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
     */
    public static function getMethodParamCallTpl(\ReflectionParameter $param): string
    {
        return ($param->isVariadic() ? '...' : '') . '$' . $param->name;
    }

    /**
     * 获取方法返回值模版.
     */
    public static function getMethodReturnType(\ReflectionMethod $method): string
    {
        if (!$method->hasReturnType())
        {
            return '';
        }
        $returnType = $method->getReturnType();

        return ': ' . ReflectionUtil::getTypeCode($returnType, $method->getDeclaringClass()->getName());
    }

    /**
     * 获取对象类名.
     *
     * @param string|object $object
     */
    public static function getObjectClass($object): string
    {
        if (\is_object($object))
        {
            if ($object instanceof IBean)
            {
                $parentClass = get_parent_class($object);
                // @phpstan-ignore-next-line
                if (false !== $parentClass)
                {
                    return $parentClass;
                }
            }

            return \get_class($object);
        }
        elseif (is_subclass_of($object, IBean::class))
        {
            return get_parent_class($object);
        }
        else
        {
            return (string) $object;
        }
    }

    /**
     * 是否有Aop注入当前方法.
     */
    public static function hasAop(\ReflectionClass $class, string $method): bool
    {
        $className = $class->getName();

        return AopManager::getBeforeItems($className, $method) || AopManager::getAfterItems($className, $method) || AopManager::getAroundItems($className, $method) || AopManager::getAfterReturningItems($className, $method) || AopManager::getAfterThrowingItems($className, $method);
    }

    public static function parseEvalName(string $class, ?string &$fileName, ?string &$className): void
    {
        $className = str_replace('\\', '__', $class) . '__Bean__';
        $fileName = Imi::getRuntimePath('classes/' . sha1($class) . '.php');
    }
}
