<?php
namespace Imi\Bean;

use Imi\Bean\Parser\BaseParser;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * 注解处理类
 */
class AnnotationParser
{
    /**
     * 处理后的数据
     * @var array
     */
    private $data = [];

    /**
     * 处理器类名映射
     * @var array
     */
    private $parsers = [];

    /**
     * 注解读取器
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    public function __construct()
    {
        // 注册注解自动加载
        AnnotationRegistry::registerLoader(function($class){
            return class_exists($class) || interface_exists($class);
        });
        $this->reader = new AnnotationReader();
    }
    
    public function parse($className)
    {
        $ref = new \ReflectionClass($className);

        // 处理类注解
        $this->parseClass($ref);

        // 处理方法注解
        $this->parseMethods($ref);

        // 处理属性注解
        $this->parseProps($ref);

        // 处理注解的处理器
        $this->parseAnnotationParsers($className);

        // 执行处理器
        $this->doParser($className);
    }

    /**
     * 处理类注解
     * @param \ReflectionClass $ref
     * @return void
     */
    public function parseClass(\ReflectionClass $ref)
    {
        $annotations = $this->reader->getClassAnnotations($ref);
        if($this->checkAnnotations($annotations))
        {
            $this->data[$ref->getName()]['class'] = $annotations;
        }
        // 是注解类的情况下，Parser类不需要指定@Parser()处理器
        else if($ref->isSubclassOf('Imi\Bean\Annotation\Base') && $ref->getName() !== 'Imi\Bean\Annotation\Parser')
        {
            throw new \Exception(sprintf('annotation %s has no @Parser()', $ref->getName()));
        }
    }

    /**
     * 处理类中方法的注解
     * @param \ReflectionClass $ref
     * @return void
     */
    public function parseMethods(\ReflectionClass $ref)
    {
        foreach($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
        {
            $this->parseMethod($ref, $method);
        }
    }

    /**
     * 处理方法注解
     * @param \ReflectionClass $ref
     * @param \ReflectionMethod $method
     * @return void
     */
    public function parseMethod(\ReflectionClass $ref, \ReflectionMethod $method)
    {
        $annotations = $this->reader->getMethodAnnotations($method);
        if($this->checkAnnotations($annotations))
        {
            $this->data[$ref->getName()]['method'][$method->getName()] = $annotations;
        }
    }

    /**
     * 处理类中属性的注解
     * @param \ReflectionClass $ref
     * @return void
     */
    public function parseProps(\ReflectionClass $ref)
    {
        foreach($ref->getProperties() as $prop)
        {
            $this->parseProp($ref, $prop);
        }
    }

    /**
     * 处理属性注解
     * @param \ReflectionClass $ref
     * @param \ReflectionProperty $prop
     * @return void
     */
    public function parseProp(\ReflectionClass $ref, \ReflectionProperty $prop)
    {
        $annotations = $this->reader->getPropertyAnnotations($prop);
        if($this->checkAnnotations($annotations))
        {
            $this->data[$ref->getName()]['prop'][$prop->getName()] = $annotations;
        }
    }

    /**
     * 检查注解
     * @param array $annotations
     * @return boolean
     */
    private function checkAnnotations($annotations)
    {
        return isset($annotations[0]);
    }

    /**
     * 处理注解的处理器
     * @return void
     */
    private function parseAnnotationParsers($className)
    {
        if(!isset($this->data[$className]))
        {
            return;
        }
        if(isset($this->parsers[$className]))
        {
            return;
        }
        $ref = new \ReflectionClass($className);
        if(!$ref->isSubclassOf('Imi\Bean\Annotation\Base'))
        {
            return;
        }
        $hasParser = false;
        foreach($this->data[$className]['class'] as $annotation)
        {
            if($annotation instanceof \Imi\Bean\Annotation\Parser)
            {
                $this->parsers[$className] = $annotation->className;
                $hasParser = true;
                break;
            }
        }
        if(!$hasParser)
        {
            throw new \Exception(sprintf('annotation %s has no @Parser()', $className));
        }
    }

    /**
     * 注解处理器是否存在
     * @param string $className
     * @return boolean
     */
    public function hasParser(string $className)
    {
        return isset($this->parsers[$className]);
    }

    /**
     * 获取注解处理器
     * @param string $className
     * @return \Imi\Bean\Parser\BaseParser
     */
    public function getParser(string $className)
    {
        return $this->parsers[$className]::getInstance();
    }

    /**
     * 执行注解处理器
     * @param string $className
     * @return void
     */
    public function doParser(string $className)
    {
        if(!isset($this->data[$className]))
        {
            return;
        }
        // 类
        if(isset($this->data[$className]['class']))
        {
            foreach($this->data[$className]['class'] as $annotation)
            {
                $annotationClassName = get_class($annotation);
                if(!$this->hasParser($annotationClassName))
                {
                    continue;
                }
                $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_CLASS, $className);
            }
        }
        // 属性
        if(isset($this->data[$className]['prop']))
        {
            foreach($this->data[$className]['prop'] as $propName => $annotations)
            {
                foreach($annotations as $annotation)
                {
                    $annotationClassName = get_class($annotation);
                    if(!$this->hasParser($annotationClassName))
                    {
                        continue;
                    }
                    $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_PROPERTY, $propName);
                }
            }
        }
        // 方法
        if(isset($this->data[$className]['method']))
        {
            foreach($this->data[$className]['method'] as $methodName => $annotations)
            {
                foreach($annotations as $annotation)
                {
                    $annotationClassName = get_class($annotation);
                    if(!$this->hasParser($annotationClassName))
                    {
                        continue;
                    }
                    $this->getParser($annotationClassName)->parse($annotation, $className, BaseParser::TARGET_METHOD, $methodName);
                }
            }
        }
    }
    
    /**
     * 获取处理后的数据
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}