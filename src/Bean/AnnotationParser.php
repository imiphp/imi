<?php

namespace Imi\Bean;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Inherit;
use Imi\Bean\Parser\BaseParser;
use Imi\Event\TEvent;
use Imi\Util\ClassObject;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;
use Yurun\Doctrine\Common\Annotations\AnnotationRegistry;

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
    private $classes = [];

    /**
     * 处理器类名映射.
     *
     * @var array
     */
    private $parsers = [];

    /**
     * 文件映射.
     *
     * @var array
     */
    private $files = [];

    /**
     * 注解读取器.
     *
     * @var \Yurun\Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    public function __construct()
    {
        // 注册注解自动加载
        AnnotationRegistry::registerLoader(function ($class) {
            return class_exists($class) || interface_exists($class);
        });
        $this->reader = new AnnotationReader();
    }

    public function parse($className)
    {
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
    }

    public function execParse($className)
    {
        // 执行处理器
        $this->doParser($className);

        // 触发完成事件
        $this->trigger('parseComplete.' . $className);
    }

    /**
     * 处理类注解.
     *
     * @param \ReflectionClass $ref
     *
     * @return void
     */
    public function parseClass(\ReflectionClass $ref)
    {
        $annotations = $this->reader->getClassAnnotations($ref);
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $className = $ref->getName();
        $thisClasses = &$this->classes;
        if ($this->checkAnnotations($annotations))
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
     *
     * @param \ReflectionClass $ref
     *
     * @return void
     */
    public function parseMethods(\ReflectionClass $ref)
    {
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            $this->parseMethod($ref, $method);
        }
    }

    /**
     * 处理方法注解.
     *
     * @param \ReflectionClass  $ref
     * @param \ReflectionMethod $method
     *
     * @return void
     */
    public function parseMethod(\ReflectionClass $ref, \ReflectionMethod $method)
    {
        $className = $ref->getName();
        $methodName = $method->getName();
        $annotations = $this->reader->getMethodAnnotations($method);
        foreach ($annotations as $i => $annotation)
        {
            if (!$annotation instanceof \Imi\Bean\Annotation\Base)
            {
                unset($annotations[$i]);
            }
        }
        $thisClasses = &$this->classes;
        if ($this->checkAnnotations($annotations))
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
     *
     * @param \ReflectionClass $ref
     *
     * @return void
     */
    public function parseProps(\ReflectionClass $ref)
    {
        foreach ($ref->getProperties() as $prop)
        {
            $this->parseProp($ref, $prop);
        }
    }

    /**
     * 处理属性注解.
     *
     * @param \ReflectionClass    $ref
     * @param \ReflectionProperty $prop
     *
     * @return void
     */
    public function parseProp(\ReflectionClass $ref, \ReflectionProperty $prop)
    {
        $annotations = $this->reader->getPropertyAnnotations($prop);
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
        if ($this->checkAnnotations($annotations))
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
     *
     * @param \ReflectionClass $ref
     *
     * @return void
     */
    public function parseConsts(\ReflectionClass $ref)
    {
        foreach ($ref->getReflectionConstants() as $const)
        {
            $this->parseConst($ref, $const);
        }
    }

    /**
     * 处理常量注解.
     *
     * @param \ReflectionClass         $ref
     * @param \ReflectionClassConstant $prop
     *
     * @return void
     */
    public function parseConst(\ReflectionClass $ref, \ReflectionClassConstant $const)
    {
        $annotations = $this->reader->getConstantAnnotations($const);
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
        if ($this->checkAnnotations($annotations))
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
     * 检查注解.
     *
     * @param array $annotations
     *
     * @return bool
     */
    private function checkAnnotations($annotations)
    {
        return [] !== $annotations;
    }

    /**
     * 处理注解的处理器.
     *
     * @return void
     */
    private function parseAnnotationParsers(\ReflectionClass $ref)
    {
        $className = $ref->getName();
        $parsers = &$this->parsers;
        if (isset($parsers[$className]))
        {
            return;
        }
        $annotations = AnnotationManager::getClassAnnotations($className);
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
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasParser(string $className)
    {
        return isset($this->parsers[$className]);
    }

    /**
     * 设置处理器数据.
     *
     * @param array $parsers
     *
     * @return void
     */
    public function setParsers($parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * 获取注解处理器.
     *
     * @param string $className
     *
     * @return \Imi\Bean\Parser\BaseParser
     */
    public function getParser(string $className)
    {
        return $this->parsers[$className]::getInstance();
    }

    /**
     * 获取所有处理器数据.
     *
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * 执行注解处理器.
     *
     * @param string $className
     *
     * @return void
     */
    public function doParser(string $className)
    {
        $classAnnotations = AnnotationManager::getClassAnnotations($className);
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
                    $annotationClassName = \get_class($annotation);
                    if ($this->hasParser($annotationClassName))
                    {
                        $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CLASS, $className);
                    }
                });
            }
        }
        // 属性
        $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className);
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
                        $annotationClassName = \get_class($annotation);
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_PROPERTY, $propName);
                        }
                    });
                }
            }
        }
        // 方法
        $methodAnnotations = AnnotationManager::getMethodsAnnotations($className);
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
                        $annotationClassName = \get_class($annotation);
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_METHOD, $methodName);
                        }
                    });
                }
            }
        }
        // 常量
        $constantAnnotations = AnnotationManager::getConstantsAnnotations($className);
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
                        $annotationClassName = \get_class($annotation);
                        if ($this->hasParser($annotationClassName))
                        {
                            $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CONST, $constName);
                        }
                    });
                }
            }
        }
    }

    /**
     * 获取类名列表.
     *
     * @return string
     */
    public function getClasses()
    {
        return array_keys($this->classes);
    }

    /**
     * Get 文件数据映射.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * 处理增量更新.
     *
     * @param string $files
     *
     * @return void
     */
    public function parseIncr($files)
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
                $this->parse($className);
            }
            else
            {
                AnnotationManager::clearClassAllAnnotations($className);
            }
            foreach (ClassObject::getSubClasses($className, $this->getClasses()) as $subClassName)
            {
                if (class_exists($subClassName))
                {
                    $this->parse($subClassName);
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
     *
     * @param string $className
     *
     * @return bool
     */
    public function isParsed($className)
    {
        return isset($this->classes[$className]);
    }

    /**
     * 获取存储数据.
     *
     * @return void
     */
    public function getStoreData()
    {
        return [
            $this->files,
            $this->classes,
        ];
    }

    /**
     * 加载存储数据.
     *
     * @param array $data
     *
     * @return void
     */
    public function loadStoreData($data)
    {
        $this->files = $data[0];
        $this->classes = $data[1];
    }
}
