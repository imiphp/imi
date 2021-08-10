<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Inherit;
use Imi\Bean\Parser\BaseParser;
use Imi\Event\TEvent;
use Imi\Util\ClassObject;
use Imi\Util\Imi;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;
use Yurun\Doctrine\Common\Annotations\FileCacheReader;
use Yurun\Doctrine\Common\Annotations\Reader;

/**
 * 注解处理类.
 */
class AnnotationParser
{
    use TEvent;

    /**
     * 类名列表.
     *
     * @var string[]
     */
    private array $classes = [];

    /**
     * 处理器类名映射.
     */
    private array $parsers = [];

    /**
     * 文件映射.
     */
    private array $files = [];

    /**
     * 注解读取器.
     */
    private Reader $reader;

    public function parse(string $className, bool $transaction = true): void
    {
        if (!class_exists($className) && !interface_exists($className, false) && !trait_exists($className, false))
        {
            return;
        }
        if ($transaction)
        {
            AnnotationManager::setRemoveWhenset(false);
        }
        $ref = ReflectionContainer::getClassReflection($className);

        // 处理类注解
        $this->parseClass($ref);

        // 处理方法注解
        $this->parseMethods($ref);

        // 处理属性注解
        $this->parseProps($ref);

        // 处理常量注解
        $this->parseConsts($ref);

        // 处理注解的处理器
        $this->parseAnnotationParsers($ref);
        if ($transaction)
        {
            AnnotationManager::setRemoveWhenset(true);
        }
    }

    public function execParse(string $className): void
    {
        // 执行处理器
        $this->doParser($className);

        // 触发完成事件
        $this->trigger('parseComplete.' . $className);
    }

    /**
     * 处理类注解.
     */
    public function parseClass(\ReflectionClass $ref): void
    {
        $annotations = $this->getReader()->getClassAnnotations($ref);
        if (version_compare(\PHP_VERSION, '8.0', '>=') && $phpAnnotations = $this->getPHPClassAnnotations($ref))
        {
            if ($annotations)
            {
                $annotations = array_merge($annotations, $phpAnnotations);
            }
            else
            {
                $annotations = $phpAnnotations;
            }
        }
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $className = $ref->getName();
        $thisClasses = &$this->classes;
        if ($annotations)
        {
            $fileName = $ref->getFileName();
            $thisClasses[$className] = $fileName;
            $this->files[$fileName] = 1;

            // @Inherit 注解继承父级的注解
            $hasInherit = false;
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof Inherit)
                {
                    $hasInherit = true;
                    break;
                }
            }
            if ($hasInherit && $parentClass = $ref->getParentClass())
            {
                $parentClassName = $parentClass->getName();
                if (!isset($thisClasses[$parentClassName]))
                {
                    $this->parse($parentClassName);
                    $this->execParse($parentClassName);
                }
                /** @var Inherit $annotation */
                if (\is_string($annotation->annotation))
                {
                    $inheritAnnotationClasses = [$annotation->annotation];
                }
                else
                {
                    $inheritAnnotationClasses = $annotation->annotation;
                }
                $inheritAnnotations = [];
                foreach (AnnotationManager::getClassAnnotations($parentClassName) as $annotation)
                {
                    if (null === $inheritAnnotationClasses)
                    {
                        $inheritAnnotations[] = $annotation;
                    }
                    else
                    {
                        foreach ($inheritAnnotationClasses as $inheritAnnotationClass)
                        {
                            if ($annotation instanceof $inheritAnnotationClass)
                            {
                                $inheritAnnotations[] = $annotation;
                                break;
                            }
                        }
                    }
                }
            }
        }
        // 是注解类的情况下，Parser类不需要指定@Parser()处理器
        elseif ($ref->isSubclassOf('Imi\Bean\Annotation\Base') && 'Imi\Bean\Annotation\Parser' !== $className)
        {
            throw new \RuntimeException(sprintf('Annotation %s has no @Parser()', $className));
        }

        AnnotationManager::setClassAnnotations($className, ...$annotations, ...$inheritAnnotations ?? []);
    }

    /**
     * 处理类中方法的注解.
     */
    public function parseMethods(\ReflectionClass $ref): void
    {
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method)
        {
            $this->parseMethod($ref, $method);
        }
    }

    /**
     * 处理方法注解.
     */
    public function parseMethod(\ReflectionClass $ref, \ReflectionMethod $method): void
    {
        $className = $ref->getName();
        $methodName = $method->getName();
        $annotations = $this->getReader()->getMethodAnnotations($method);
        if (version_compare(\PHP_VERSION, '8.0', '>=') && $phpAnnotations = $this->getPHPMethodAnnotations($method))
        {
            if ($annotations)
            {
                $annotations = array_merge($annotations, $phpAnnotations);
            }
            else
            {
                $annotations = $phpAnnotations;
            }
        }
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $thisClasses = &$this->classes;
        if ($annotations)
        {
            $fileName = $ref->getFileName();
            $thisClasses[$className] = $fileName;
            $this->files[$fileName] = 1;

            // @Inherit 注解继承父级的注解
            $hasInherit = false;
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof Inherit)
                {
                    $hasInherit = true;
                    break;
                }
            }
            if ($hasInherit && $parentClass = $ref->getParentClass())
            {
                $parentClassName = $parentClass->getName();
                if (!isset($thisClasses[$parentClassName]))
                {
                    $this->parse($parentClassName);
                    $this->execParse($parentClassName);
                }
                if (\is_string($annotation->annotation))
                {
                    $inheritAnnotationClasses = [$annotation->annotation];
                }
                else
                {
                    $inheritAnnotationClasses = $annotation->annotation;
                }
                $inheritAnnotations = [];
                foreach (AnnotationManager::getMethodAnnotations($parentClassName, $methodName) as $annotation)
                {
                    if (null === $inheritAnnotationClasses)
                    {
                        $inheritAnnotations[] = $annotation;
                    }
                    else
                    {
                        foreach ($inheritAnnotationClasses as $inheritAnnotationClass)
                        {
                            if ($annotation instanceof $inheritAnnotationClass)
                            {
                                $inheritAnnotations[] = $annotation;
                                break;
                            }
                        }
                    }
                }
            }
        }

        AnnotationManager::setMethodAnnotations($className, $methodName, ...$annotations, ...$inheritAnnotations ?? []);
    }

    /**
     * 处理类中属性的注解.
     */
    public function parseProps(\ReflectionClass $ref): void
    {
        foreach ($ref->getProperties() as $prop)
        {
            $this->parseProp($ref, $prop);
        }
    }

    /**
     * 处理属性注解.
     */
    public function parseProp(\ReflectionClass $ref, \ReflectionProperty $prop): void
    {
        $annotations = $this->getReader()->getPropertyAnnotations($prop);
        if (version_compare(\PHP_VERSION, '8.0', '>=') && $phpAnnotations = $this->getPHPPropertyAnnotations($prop))
        {
            if ($annotations)
            {
                $annotations = array_merge($annotations, $phpAnnotations);
            }
            else
            {
                $annotations = $phpAnnotations;
            }
        }
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $className = $ref->getName();
        $propertyName = $prop->getName();
        $thisClasses = &$this->classes;
        if ($annotations)
        {
            $fileName = $ref->getFileName();
            $thisClasses[$className] = $fileName;
            $this->files[$fileName] = 1;

            // @Inherit 注解继承父级的注解
            $hasInherit = false;
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof Inherit)
                {
                    $hasInherit = true;
                    break;
                }
            }
            if ($hasInherit && $parentClass = $ref->getParentClass())
            {
                $parentClassName = $parentClass->getName();
                if (!isset($thisClasses[$parentClassName]))
                {
                    $this->parse($parentClassName);
                    $this->execParse($parentClassName);
                }
                if (\is_string($annotation->annotation))
                {
                    $inheritAnnotationClasses = [$annotation->annotation];
                }
                else
                {
                    $inheritAnnotationClasses = $annotation->annotation;
                }
                $inheritAnnotations = [];
                foreach (AnnotationManager::getPropertyAnnotations($parentClassName, $propertyName) as $annotation)
                {
                    if (null === $inheritAnnotationClasses)
                    {
                        $inheritAnnotations[] = $annotation;
                    }
                    else
                    {
                        foreach ($inheritAnnotationClasses as $inheritAnnotationClass)
                        {
                            if ($annotation instanceof $inheritAnnotationClass)
                            {
                                $inheritAnnotations[] = $annotation;
                                break;
                            }
                        }
                    }
                }
            }
        }

        AnnotationManager::setPropertyAnnotations($className, $propertyName, ...$annotations, ...$inheritAnnotations ?? []);
    }

    /**
     * 处理类中常量的注解.
     */
    public function parseConsts(\ReflectionClass $ref): void
    {
        foreach ($ref->getReflectionConstants() as $const)
        {
            $this->parseConst($ref, $const);
        }
    }

    /**
     * 处理常量注解.
     */
    public function parseConst(\ReflectionClass $ref, \ReflectionClassConstant $const): void
    {
        $annotations = $this->getReader()->getConstantAnnotations($const);
        if (version_compare(\PHP_VERSION, '8.0', '>=') && $phpAnnotations = $this->getPHPConstantAnnotations($const))
        {
            if ($annotations)
            {
                $annotations = array_merge($annotations, $phpAnnotations);
            }
            else
            {
                $annotations = $phpAnnotations;
            }
        }
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $className = $ref->getName();
        $constName = $const->getName();
        $thisClasses = &$this->classes;
        if ($annotations)
        {
            $fileName = $ref->getFileName();
            $thisClasses[$className] = $fileName;
            $this->files[$fileName] = 1;

            // @Inherit 注解继承父级的注解
            $hasInherit = false;
            $annotation = null;
            foreach ($annotations as $annotation)
            {
                if ($annotation instanceof Inherit)
                {
                    $hasInherit = true;
                    break;
                }
            }
            if ($hasInherit && $parentClass = $ref->getParentClass())
            {
                $parentClassName = $parentClass->getName();
                if (!isset($thisClasses[$parentClassName]))
                {
                    $this->parse($parentClassName);
                    $this->execParse($parentClassName);
                }
                /** @var Inherit $annotation */
                if (\is_string($annotation->annotation))
                {
                    $inheritAnnotationClasses = [$annotation->annotation];
                }
                else
                {
                    $inheritAnnotationClasses = $annotation->annotation;
                }
                $inheritAnnotations = [];
                foreach (AnnotationManager::getConstantAnnotations($parentClassName, $constName) as $annotation)
                {
                    if (null === $inheritAnnotationClasses)
                    {
                        $inheritAnnotations[] = $annotation;
                    }
                    else
                    {
                        foreach ($inheritAnnotationClasses as $inheritAnnotationClass)
                        {
                            if ($annotation instanceof $inheritAnnotationClass)
                            {
                                $inheritAnnotations[] = $annotation;
                                break;
                            }
                        }
                    }
                }
            }
        }

        AnnotationManager::setConstantAnnotations($className, $constName, ...$annotations, ...$inheritAnnotations ?? []);
    }

    /**
     * 处理注解的处理器.
     */
    private function parseAnnotationParsers(\ReflectionClass $ref): void
    {
        $className = $ref->getName();
        $parsers = &$this->parsers;
        if (isset($parsers[$className]))
        {
            return;
        }
        $annotations = AnnotationManager::getClassAnnotations($className, null, false);
        if (!isset($annotations[0]))
        {
            return;
        }
        if (!$ref->isSubclassOf('Imi\Bean\Annotation\Base'))
        {
            return;
        }
        $hasParser = false;
        foreach ($annotations as $annotation)
        {
            if ($annotation instanceof \Imi\Bean\Annotation\Parser)
            {
                $parsers[$className] = $annotation->className;
                $hasParser = true;
                break;
            }
        }
        if (!$hasParser)
        {
            throw new \RuntimeException(sprintf('Annotation %s has no @Parser()', $className));
        }
    }

    /**
     * 注解处理器是否存在.
     */
    public function hasParser(string $className): bool
    {
        return isset($this->parsers[$className]);
    }

    /**
     * 设置处理器数据.
     */
    public function setParsers(array $parsers): void
    {
        $this->parsers = $parsers;
    }

    /**
     * 获取注解处理器.
     */
    public function getParser(string $className): BaseParser
    {
        return $this->parsers[$className]::getInstance();
    }

    /**
     * 获取所有处理器数据.
     */
    public function getParsers(): array
    {
        return $this->parsers;
    }

    /**
     * 执行注解处理器.
     */
    public function doParser(string $className): void
    {
        $classAnnotations = AnnotationManager::getClassAnnotations($className, null, false);
        // 类
        foreach ($classAnnotations as $annotation)
        {
            $annotationClassName = \get_class($annotation);
            if ($this->hasParser($annotationClassName))
            {
                $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CLASS, $className);
            }
            else
            {
                $this->one('parseComplete.' . $annotationClassName, function () use ($annotationClassName, $annotation, $className) {
                    if ($this->hasParser($annotationClassName))
                    {
                        $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CLASS, $className);
                    }
                }, 10);
            }
        }
        // 属性
        $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className, null, false);
        foreach ($propertyAnnotations as $propName => $annotations)
        {
            foreach ($annotations as $annotation)
            {
                $annotationClassName = \get_class($annotation);
                if ($this->hasParser($annotationClassName))
                {
                    $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_PROPERTY, $propName);
                }
                else
                {
                    $this->one('parseComplete.' . $annotationClassName, function () use ($annotationClassName, $annotation, $className, $propName) {
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_PROPERTY, $propName);
                        }
                    }, 9);
                }
            }
        }
        // 方法
        $methodAnnotations = AnnotationManager::getMethodsAnnotations($className, null, false);
        foreach ($methodAnnotations as $methodName => $annotations)
        {
            foreach ($annotations as $annotation)
            {
                $annotationClassName = \get_class($annotation);
                if ($this->hasParser($annotationClassName))
                {
                    $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_METHOD, $methodName);
                }
                else
                {
                    $this->one('parseComplete.' . $annotationClassName, function () use ($annotationClassName, $annotation, $className, $methodName) {
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_METHOD, $methodName);
                        }
                    }, 8);
                }
            }
        }
        // 常量
        $constantAnnotations = AnnotationManager::getConstantsAnnotations($className, null, false);
        foreach ($constantAnnotations as $constName => $annotations)
        {
            foreach ($annotations as $annotation)
            {
                $annotationClassName = \get_class($annotation);
                if ($this->hasParser($annotationClassName))
                {
                    $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CONST, $constName);
                }
                else
                {
                    $this->one('parseComplete.' . $annotationClassName, function () use ($annotationClassName, $annotation, $className, $constName) {
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CONST, $constName);
                        }
                    }, 7);
                }
            }
        }
    }

    /**
     * 获取类名列表.
     *
     * @return string[]
     */
    public function getClasses(): array
    {
        return array_keys($this->classes);
    }

    /**
     * Get 文件数据映射.
     *
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * 处理增量更新.
     *
     * @param string[] $files
     */
    public function parseIncr(array $files): void
    {
        $thisFiles = &$this->files;
        $thisClasses = &$this->classes;
        foreach ($files as $file)
        {
            if (isset($thisFiles[$file]))
            {
                unset($thisFiles[$file]);
            }
            $className = null;
            if ($className = array_search($file, $thisClasses))
            {
            }
            elseif (is_file($file))
            {
                $content = file_get_contents($file);
                if (preg_match('/namespace ([^;]+);/', $content, $matches) <= 0)
                {
                    continue;
                }
                $namespace = trim($matches[1]);
                $className = $namespace . '\\' . basename($file, '.php');
            }
            else
            {
                continue;
            }
            if (class_exists($className))
            {
                $this->parse($className, false);
            }
            else
            {
                AnnotationManager::clearClassAllAnnotations($className);
            }
            foreach (ClassObject::getSubClasses($className, $this->getClasses()) as $subClassName)
            {
                if (class_exists($subClassName))
                {
                    $this->parse($subClassName, false);
                }
                else
                {
                    AnnotationManager::clearClassAllAnnotations($subClassName);
                }
            }
        }
    }

    /**
     * 是否处理过该类.
     */
    public function isParsed(string $className): bool
    {
        return isset($this->classes[$className]);
    }

    /**
     * 获取存储数据.
     */
    public function getStoreData(): array
    {
        return [
            $this->files,
            $this->classes,
        ];
    }

    /**
     * 加载存储数据.
     */
    public function loadStoreData(array $data): void
    {
        $this->files = $data[0];
        $this->classes = $data[1];
    }

    /**
     * Get 注解读取器.
     */
    private function getReader(): Reader
    {
        if (isset($this->reader))
        {
            return $this->reader;
        }
        if ('cli' === \PHP_SAPI)
        {
            return $this->reader = new AnnotationReader();
        }
        else
        {
            return $this->reader = new FileCacheReader(new AnnotationReader(), Imi::getRuntimePath('annotation'));
        }
    }

    /**
     * 获取类的 PHP 原生注解.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getPHPClassAnnotations(\ReflectionClass $reflectionClass): array
    {
        $annotations = [];
        foreach ($reflectionClass->getAttributes() as $attribute)
        {
            $class = $attribute->getName();
            $annotations[] = new $class(...$attribute->getArguments());
        }

        return $annotations;
    }

    /**
     * 获取方法的 PHP 原生注解.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getPHPMethodAnnotations(\ReflectionMethod $reflectionMethod): array
    {
        $annotations = [];
        foreach ($reflectionMethod->getAttributes() as $attribute)
        {
            $class = $attribute->getName();
            $annotations[] = new $class(...$attribute->getArguments());
        }

        return $annotations;
    }

    /**
     * 获取属性的 PHP 原生注解.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getPHPPropertyAnnotations(\ReflectionProperty $reflectionProperty): array
    {
        $annotations = [];
        foreach ($reflectionProperty->getAttributes() as $attribute)
        {
            $class = $attribute->getName();
            $annotations[] = new $class(...$attribute->getArguments());
        }

        return $annotations;
    }

    /**
     * 获取类常量的 PHP 原生注解.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getPHPConstantAnnotations(\ReflectionClassConstant $reflectionConstant): array
    {
        $annotations = [];
        foreach ($reflectionConstant->getAttributes() as $attribute)
        {
            $class = $attribute->getName();
            $annotations[] = new $class(...$attribute->getArguments());
        }

        return $annotations;
    }
}
